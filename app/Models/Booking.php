<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Notifications\Notifiable;
use App\Services\CreditService;

class Booking extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory, Notifiable;

    public $table = 'bookings';

    protected $appends = [
        'photos',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'from',
        'to',
        'start_time',
        'end_time',
    ];

    protected $fillable = [
        'pet_id',
        'from',
        'from_time',
        'to',
        'to_time',
        'start_time',
        'end_time',
        'status',
        'user_id',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'pending'   => 'Pending',
        'accepted'  => 'Accepted',
        'rejected'  => 'Rejected',
        'completed' => 'Completed',
        'new'       => 'New',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot()
    {
        parent::boot();
        self::observe(new \App\Observers\BookingActionObserver);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function getPhotosAttribute()
    {
        $files = $this->getMedia('photos');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function photos()
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * Complete the booking and award credits to the user
     *
     * @return bool
     */
    public function complete()
    {
        if ($this->status !== 'completed') {
            $this->status = 'completed';
            $this->save();

            // Award credits to the user
            $creditService = app(CreditService::class);
            $creditService->awardCreditsForBooking($this->user, $this);

            return true;
        }

        return false;
    }

    /**
     * Check if the user has enough credits for this booking
     *
     * @return bool
     */
    public function userHasEnoughCredits()
    {
        $creditService = app(CreditService::class);
        return $creditService->hasEnoughCreditsForBooking($this->user, $this);
    }

    /**
     * Deduct credits from the user for this booking
     *
     * @return bool
     */
    public function deductUserCredits()
    {
        $creditService = app(CreditService::class);
        return $creditService->deductCreditsForBooking($this->user, $this);
    }
}

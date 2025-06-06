<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Pet extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'pets';

    protected $appends = [
        'photo',
    ];

    public const TYPE_SELECT = [
        'Cat' => 'Cat',
        'Dog' => 'Dog',
    ];

    public const GENDER_SELECT = [
        'Male'   => 'Male',
        'Female' => 'Female',
    ];

    protected $dates = [
        'from',
        'to',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'type',
        'name',
        'age',
        'gender',
        'not_available',
        'from',
        'from_time',
        'to',
        'to_time',
        'user_id',
        'featured_until',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'featured_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function notifications()
    {
        return $this->hasMany(PetNotification::class);
    }

    public function petReviews()
    {
        return $this->hasMany(PetReview::class, 'pet_id');
    }

    public function photo()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', 'photo');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 267, 200);
    }

    public function getPhotoAttribute()
    {
        $files = $this->getMedia('photo');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function getFromAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setFromAttribute($value)
    {
        $this->attributes['from'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getToAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setToAttribute($value)
    {
        $this->attributes['to'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function isFeatured()
    {
        return $this->featured_until && $this->featured_until->isFuture();
    }

    public function canBeFeatured()
    {
        return $this->user->isPremium() && !$this->isFeatured();
    }

    public function feature()
    {
        if (!$this->canBeFeatured()) {
            return false;
        }

        $this->featured_until = now()->addHour();
        return $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($pet) {
            // Check if the pet just became available
            if ($pet->wasChanged('not_available') && !$pet->not_available) {
                app(PetNotificationService::class)->notifyPreviousCaretakers($pet);
            }
        });
    }
}

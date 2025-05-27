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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Booking extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory, Notifiable;

    private CreditService $creditService;

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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->creditService = app(CreditService::class); // Default instantiation, can be overridden by setter or tests
    }

    // Optional: A setter method if direct injection post-construction is needed, though constructor is preferred.
    // public function setCreditService(CreditService $creditService)
    // {
    //     $this->creditService = $creditService;
    // }

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

        // Populate start_time and end_time from from/to and from_time/to_time
        static::saving(function ($model) {
            if (isset($model->from) && isset($model->from_time)) {
                // Ensure 'from' is treated as date-only before combining
                $fromDate = Carbon::parse($model->from)->format('Y-m-d');
                $model->start_time = Carbon::parse($fromDate . ' ' . $model->from_time);
            }
            if (isset($model->to) && isset($model->to_time)) {
                // Ensure 'to' is treated as date-only before combining
                $toDate = Carbon::parse($model->to)->format('Y-m-d');
                $model->end_time = Carbon::parse($toDate . ' ' . $model->to_time);
            }
        });
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

    /**
     * Complete the booking and award credits to the user
     *
     * @return bool
     */
    public function complete()
    {
        if ($this->status !== 'completed') {
            try {
                DB::beginTransaction();

                // Load necessary relationships
                $this->load(['pet.user', 'user']);
                
                if (!$this->pet || !$this->pet->user) {
                    throw new \Exception('Pet or pet owner not found for this booking.');
                }

                if (!$this->user) {
                    throw new \Exception('Caregiver not found for this booking.');
                }

                // Calculate hours using the service
                $hours = $this->creditService->calculateBookingHours($this);

                // Deduct credits from pet owner
                if (!$this->creditService->deductCreditsForBooking($this->pet->user, $this)) {
                    throw new \Exception('Failed to deduct credits from pet owner.');
                }

                // Award credits to caregiver
                if (!$this->creditService->awardCreditsForBooking($this->user, $this)) {
                    throw new \Exception('Failed to award credits to caregiver.');
                }

                // Update booking status
                $this->status = 'completed';
                $this->save();

                // Set pet as unavailable and clear booking dates
                $this->pet->not_available = true;
                $this->pet->from = null;
                $this->pet->from_time = null;
                $this->pet->to = null;
                $this->pet->to_time = null;
                $this->pet->save();

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error completing booking', [
                    'booking_id' => $this->id,
                    'error' => $e->getMessage()
                ]);
                return false;
            }
        }

        return false;
    }

    /**
     * Cancel the booking and refund credits
     *
     * @return bool
     */
    public function cancel()
    {
        if ($this->status === 'pending') {
            try {
                DB::beginTransaction();

                // Load necessary relationships
                $this->load(['pet.user', 'user']);
                
                if (!$this->pet || !$this->pet->user) {
                    throw new \Exception('Pet or pet owner not found for this booking.');
                }

                // Refund credits to pet owner
                if (!$this->creditService->refundCreditsForBooking($this->pet->user, $this)) {
                    throw new \Exception('Failed to refund credits to pet owner.');
                }

                // Update booking status
                $this->status = 'cancelled';
                $this->save();

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error cancelling booking', [
                    'booking_id' => $this->id,
                    'error' => $e->getMessage()
                ]);
                return false;
            }
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
        return $this->creditService->hasEnoughCreditsForBooking($this->user, $this);
    }

    /**
     * Deduct credits from the user for this booking
     *
     * @return bool
     */
    public function deductUserCredits()
    {
        return $this->creditService->deductCreditsForBooking($this->user, $this);
    }
}

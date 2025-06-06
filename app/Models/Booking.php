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
        'expired'   => 'Expired',
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

                // Get credit service instance
                $creditService = app(CreditService::class);
                
                // Calculate hours using the service
                $hours = $creditService->calculateBookingHours($this);

                // Deduct credits from pet owner
                if (!$creditService->deductCreditsForBooking($this->pet->user, $this)) {
                    throw new \Exception('Failed to deduct credits from pet owner.');
                }

                // Award credits to caregiver
                if (!$creditService->awardCreditsForBooking($this->user, $this)) {
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

                // Get credit service instance
                $creditService = app(CreditService::class);
                
                // Refund credits to pet owner
                if (!$creditService->refundCreditsForBooking($this->pet->user, $this)) {
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

    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('to', '<', now()->format('Y-m-d'))
                        ->orWhere(function ($q) {
                            $q->where('to', '=', now()->format('Y-m-d'))
                                ->where('to_time', '<', now()->format('H:i:s'));
                        });
                });
            });
    }

    public function getStatusColorAttribute()
    {
        if ($this->isExpired()) {
            return 'secondary';
        }
        
        return match($this->status) {
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            'completed' => 'info',
            'new' => 'success',
            'expired' => 'secondary',
            default => 'secondary',
        };
    }

    public function isExpired()
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $endDateTime = \Carbon\Carbon::parse($this->to . ' ' . $this->to_time);
        return $endDateTime->isPast();
    }

    public function getStatusTextAttribute()
    {
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        return App\Models\Booking::STATUS_SELECT[$this->status] ?? 'Unknown';
    }
}

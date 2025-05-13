<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Pet;
use App\Models\User;
use App\Notifications\DataChangeEmailNotification;
use App\Notifications\BookingStatusNotification;
use Illuminate\Support\Facades\Notification;

class BookingActionObserver
{
    public function created(Booking $model)
    {
        // Notify pet owner about new booking request
        $petOwner = $model->pet->user;
        $petOwner->notify(new BookingStatusNotification($model, 'new'));

        // Notify admins
        $data = ['action' => 'created', 'model_name' => 'Booking'];
        $users = User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
      //  Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function updated(Booking $model)
    {
        // Update pet availability based on booking status
        if ($model->isDirty('status')) {
            $pet = Pet::find($model->pet_id);
            if ($pet) {
                $pet->not_available = $model->status === 'accepted';
                $pet->save();

                // Send notification based on status change
                switch ($model->status) {
                    case 'accepted':
                        // Notify the requester
                        $model->user->notify(new BookingStatusNotification($model, 'accepted'));
                        break;
                    case 'rejected':
                        // Notify the requester
                        $model->user->notify(new BookingStatusNotification($model, 'rejected'));
                        break;
                    case 'completed':
                        // Notify both parties
                        $model->user->notify(new BookingStatusNotification($model, 'completed'));
                        $model->pet->user->notify(new BookingStatusNotification($model, 'completed'));
                        break;
                }
            }
        }

        // Notify admins
        $data = ['action' => 'updated', 'model_name' => 'Booking'];
        $users = User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
       // Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function deleting(Booking $model)
    {
        // Notify admins
        $data = ['action' => 'deleted', 'model_name' => 'Booking'];
        $users = User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
      //  Notification::send($users, new DataChangeEmailNotification($data));
    }
}

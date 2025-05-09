<?php

namespace App\Observers;

use App\Models\PetReview;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class PetReviewActionObserver
{
    public function created(PetReview $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'PetReview'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}

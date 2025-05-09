<?php

namespace App\Observers;

use App\Models\Support;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class SupportActionObserver
{
    public function created(Support $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'Support'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}

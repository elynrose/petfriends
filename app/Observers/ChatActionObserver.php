<?php

namespace App\Observers;

use App\Models\Chat;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class ChatActionObserver
{
    public function created(Chat $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'Chat'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function updated(Chat $model)
    {
        $data  = ['action' => 'updated', 'model_name' => 'Chat'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function deleting(Chat $model)
    {
        $data  = ['action' => 'deleted', 'model_name' => 'Chat'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}

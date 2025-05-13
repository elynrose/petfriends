<?php

namespace App\Policies;

use App\Models\Credit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Credit $credit)
    {
        return $user->id === $credit->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Credit $credit)
    {
        return $user->id === $credit->user_id;
    }

    public function delete(User $user, Credit $credit)
    {
        return $user->id === $credit->user_id;
    }

    public function restore(User $user, Credit $credit)
    {
        return $user->id === $credit->user_id;
    }

    public function forceDelete(User $user, Credit $credit)
    {
        return $user->id === $credit->user_id;
    }
} 
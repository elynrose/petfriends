<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Pet $pet)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Pet $pet)
    {
        return $user->id === $pet->user_id;
    }

    public function delete(User $user, Pet $pet)
    {
        return $user->id === $pet->user_id;
    }

    public function restore(User $user, Pet $pet)
    {
        return $user->id === $pet->user_id;
    }

    public function forceDelete(User $user, Pet $pet)
    {
        return $user->id === $pet->user_id;
    }
} 
<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SuperAdmin);
    }

    /**
     * Determine whether the user can create a user.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::SuperAdmin);
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasRole(UserRole::SuperAdmin);
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole(UserRole::SuperAdmin);
    }
}

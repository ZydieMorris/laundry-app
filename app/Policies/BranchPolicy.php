<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    /**
     * Determine whether the user can view any branches.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SuperAdmin);
    }

    /**
     * Determine whether the user can create a branch.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::SuperAdmin);
    }

    /**
     * Determine whether the user can update the branch.
     */
    public function update(User $user, Branch $branch): bool
    {
        return $user->hasRole(UserRole::SuperAdmin);
    }
}

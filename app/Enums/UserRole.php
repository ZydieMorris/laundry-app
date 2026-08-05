<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case BranchAdmin = 'branch_admin';
    case Staff = 'staff';
    case Customer = 'customer';

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::BranchAdmin => 'Branch Admin',
            self::Staff => 'Staff',
            self::Customer => 'Customer',
        };
    }

    /**
     * Get the route name of the role's dashboard.
     */
    public function homeRoute(): string
    {
        return match ($this) {
            self::SuperAdmin => 'superadmin.dashboard',
            self::BranchAdmin => 'branch_admin.dashboard',
            self::Staff => 'staff.dashboard',
            self::Customer => 'customer.dashboard',
        };
    }
}

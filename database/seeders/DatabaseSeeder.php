<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $this->createUserWithRole('Super Admin', 'superadmin@laundry.test', UserRole::SuperAdmin);
        $this->createUserWithRole('Admin', 'admin@laundry.test', UserRole::Admin);
        $this->createUserWithRole('Staff', 'staff@laundry.test', UserRole::Staff);
        $this->createUserWithRole('Test User', 'test@example.com', UserRole::Customer);
    }

    /**
     * Create a user and assign a single role.
     */
    private function createUserWithRole(string $name, string $email, UserRole $role): void
    {
        User::factory()->create([
            'name' => $name,
            'email' => $email,
        ])->assignRole($role->value);
    }
}
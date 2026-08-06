<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Branch;
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
        $this->call(BranchSeeder::class);

        $this->createUserWithRole('Super Admin', 'superadmin@laundry.test', UserRole::SuperAdmin);
        $this->createUserWithRole('Branch Admin', 'admin@laundry.test', UserRole::BranchAdmin, 'BR-RIVERSIDE');
        $this->createUserWithRole('Staff', 'staff@laundry.test', UserRole::Staff, 'BR-WESTSIDE');
        $this->createUserWithRole('Test User', 'test@example.com', UserRole::Customer);
    }

    /**
     * Create a user and assign a single role.
     */
    private function createUserWithRole(string $name, string $email, UserRole $role, ?string $branchCode = null): void
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
        ])->assignRole($role->value);

        if ($branchCode !== null) {
            $user->update(['branch_id' => Branch::where('code', $branchCode)->value('id')]);
        }
    }
}

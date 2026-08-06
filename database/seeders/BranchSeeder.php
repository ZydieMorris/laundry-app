<?php

namespace Database\Seeders;

use App\Enums\BranchStatus;
use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's branches.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Downtown Laundry',
                'owner' => 'Maria Santos',
                'code' => 'BR-DOWNTOWN',
                'address' => '123 Main Street, Downtown District',
                'status' => BranchStatus::Active,
            ],
            [
                'name' => 'Riverside Express',
                'owner' => 'James Rivera',
                'code' => 'BR-RIVERSIDE',
                'address' => '45 Riverside Avenue',
                'status' => BranchStatus::Active,
            ],
            [
                'name' => 'Westside Wash',
                'owner' => 'Angela Cruz',
                'code' => 'BR-WESTSIDE',
                'address' => '88 Westside Boulevard',
                'status' => BranchStatus::Active,
            ],
            [
                'name' => 'Harbor Cleaners',
                'owner' => 'Robert Tan',
                'code' => 'BR-HARBOR',
                'address' => '210 Harbor Point Road',
                'status' => BranchStatus::Inactive,
            ],
            [
                'name' => 'Uptown Fresh',
                'owner' => 'Sofia Reyes',
                'code' => 'BR-UPTOWN',
                'address' => '17 Uptown Plaza, Level 2',
                'status' => BranchStatus::Active,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}

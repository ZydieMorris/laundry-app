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
                'code' => 'BR-DOWNTOWN',
                'contact_number' => '+1 555-0101',
                'email' => 'downtown@laundry.test',
                'address' => '123 Main Street, Downtown District',
                'status' => BranchStatus::Active,
            ],
            [
                'name' => 'Riverside Express',
                'code' => 'BR-RIVERSIDE',
                'contact_number' => '+1 555-0102',
                'email' => 'riverside@laundry.test',
                'address' => '45 Riverside Avenue',
                'status' => BranchStatus::Active,
            ],
            [
                'name' => 'Westside Wash',
                'code' => 'BR-WESTSIDE',
                'contact_number' => '+1 555-0103',
                'email' => 'westside@laundry.test',
                'address' => '88 Westside Boulevard',
                'status' => BranchStatus::Active,
            ],
            [
                'name' => 'Harbor Cleaners',
                'code' => 'BR-HARBOR',
                'contact_number' => '+1 555-0104',
                'email' => 'harbor@laundry.test',
                'address' => '210 Harbor Point Road',
                'status' => BranchStatus::Inactive,
            ],
            [
                'name' => 'Uptown Fresh',
                'code' => 'BR-UPTOWN',
                'contact_number' => '+1 555-0105',
                'email' => 'uptown@laundry.test',
                'address' => '17 Uptown Plaza, Level 2',
                'status' => BranchStatus::Active,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}

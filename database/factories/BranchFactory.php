<?php

namespace Database\Factories;

use App\Enums\BranchStatus;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company().' Laundry',
            'owner' => $this->faker->name(),
            'code' => strtoupper('BR-'.$this->faker->unique()->bothify('####??')),
            'address' => $this->faker->address(),
            'status' => BranchStatus::Active,
        ];
    }

    /**
     * Indicate that the branch is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => BranchStatus::Inactive,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Campus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampusAreaFactory extends Factory
{
    public function definition(): array
    {
        return ['campus_id' => Campus::factory(), 'name' => fake()->unique()->streetName(), 'is_active' => true];
    }
}

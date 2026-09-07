<?php

namespace Database\Factories;

use App\Models\CampusArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampusLocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campus_area_id' => CampusArea::factory(),
            'name' => fake()->unique()->company(),
            'location_type' => 'building',
            'description' => fake()->sentence(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'accessibility_status' => 'not_assessed',
            'is_active' => true,
        ];
    }
}

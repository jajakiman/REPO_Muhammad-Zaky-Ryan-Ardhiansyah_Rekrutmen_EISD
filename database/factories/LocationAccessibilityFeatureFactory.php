<?php

namespace Database\Factories;

use App\Models\AccessibilityFeature;
use App\Models\CampusLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationAccessibilityFeatureFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campus_location_id' => CampusLocation::factory(),
            'accessibility_feature_id' => AccessibilityFeature::factory(),
            'availability_status' => 'available',
            'condition' => 'good',
            'notes' => null,
            'last_checked_at' => null,
        ];
    }
}

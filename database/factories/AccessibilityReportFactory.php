<?php

namespace Database\Factories;

use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessibilityReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'report_code' => fake()->unique()->numerify('RPT-20260907-####'),
            'reporter_id' => User::factory(),
            'officer_id' => null,
            'location_accessibility_feature_id' => LocationAccessibilityFeature::factory(),
            'issue_category_id' => IssueCategory::factory(),
            'description' => fake()->paragraph(),
            'photo_path' => null,
            'status' => 'submitted',
            'priority' => null,
            'rejection_reason' => null,
            'resolution_notes' => null,
            'resolution_photo_path' => null,
            'verified_at' => null,
            'handling_started_at' => null,
            'resolved_at' => null,
        ];
    }
}

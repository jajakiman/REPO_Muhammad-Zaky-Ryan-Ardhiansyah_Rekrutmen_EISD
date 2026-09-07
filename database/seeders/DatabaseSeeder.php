<?php

namespace Database\Seeders;

use App\Models\AccessibilityFeature;
use App\Models\Campus;
use App\Models\IssueCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Telkom University Bandung', 'Universitas Pendidikan Indonesia', 'Universitas Teknologi Bandung'] as $name) {
            Campus::updateOrCreate(['name' => $name], ['is_active' => true]);
        }

        foreach (['Ramp', 'Lift', 'Guiding Block', 'Toilet Aksesibel', 'Handrail', 'Parkir Disabilitas', 'Pintu Aksesibel'] as $name) {
            AccessibilityFeature::updateOrCreate(['name' => $name], ['is_active' => true]);
        }

        foreach (['Fasilitas Rusak', 'Akses Terhalang', 'Tidak Dapat Digunakan', 'Permukaan Tidak Aman', 'Penerangan Tidak Memadai', 'Signage Tidak Jelas'] as $name) {
            IssueCategory::updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\AccessibilityFeature;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use App\Models\User;
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

        User::updateOrCreate(
            ['email' => 'admin@aksesloka.id'],
            [
                'name' => 'Administrator Pusat',
                'password' => 'Password123!',
                'role' => 'admin',
                'affiliation_type' => 'staff',
                'campus_id' => null,
                'campus_area_id' => null,
                'is_active' => true,
            ],
        );

        $demoCampuses = [
            'Telkom University Bandung' => [
                'area' => 'Kawasan Gedung Kuliah Bersama (GKB)',
                'officer' => ['Petugas Telkom University', 'petugas.telkom@aksesloka.id'],
                'location' => ['Gedung Tokong Nanas', -6.9730780, 107.6302810],
                'features' => ['Ramp', 'Lift', 'Toilet Aksesibel'],
            ],
            'Universitas Pendidikan Indonesia' => [
                'area' => 'Kawasan FPMIPA & Gymnasium',
                'officer' => ['Petugas UPI Bandung', 'petugas.upi@aksesloka.id'],
                'location' => ['Gedung Isola', -6.8607250, 107.5944550],
                'features' => ['Ramp', 'Guiding Block', 'Handrail'],
            ],
            'Universitas Teknologi Bandung' => [
                'area' => 'Kawasan Kampus Utama',
                'officer' => ['Petugas UTB Bandung', 'petugas.utb@aksesloka.id'],
                'location' => ['Gedung Magnesit', -6.8634000, 107.6092000],
                'features' => ['Ramp', 'Parkir Disabilitas', 'Pintu Aksesibel'],
            ],
        ];

        foreach ($demoCampuses as $campusName => $demo) {
            $campus = Campus::where('name', $campusName)->firstOrFail();
            $area = CampusArea::updateOrCreate(
                ['campus_id' => $campus->id, 'name' => $demo['area']],
                ['is_active' => true],
            );

            User::updateOrCreate(
                ['email' => $demo['officer'][1]],
                [
                    'name' => $demo['officer'][0],
                    'password' => 'Password123!',
                    'role' => 'officer',
                    'affiliation_type' => 'staff',
                    'campus_id' => $campus->id,
                    'campus_area_id' => $area->id,
                    'is_active' => true,
                ],
            );

            $location = CampusLocation::updateOrCreate(
                ['campus_area_id' => $area->id, 'name' => $demo['location'][0]],
                [
                    'location_type' => 'building',
                    'description' => 'Lokasi contoh untuk demonstrasi peta dan pelaporan aksesibilitas.',
                    'latitude' => $demo['location'][1],
                    'longitude' => $demo['location'][2],
                    'accessibility_status' => 'accessible',
                    'is_active' => true,
                ],
            );

            foreach ($demo['features'] as $featureName) {
                $feature = AccessibilityFeature::where('name', $featureName)->firstOrFail();

                LocationAccessibilityFeature::updateOrCreate(
                    [
                        'campus_location_id' => $location->id,
                        'accessibility_feature_id' => $feature->id,
                    ],
                    [
                        'availability_status' => 'available',
                        'condition' => 'good',
                        'notes' => 'Data fasilitas awal untuk demonstrasi.',
                        'last_checked_at' => now(),
                    ],
                );
            }
        }
    }
}

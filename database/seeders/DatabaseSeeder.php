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

        $features = [
            'Jalur Landai (Ramp)',
            'Lift Aksesibel',
            'Ubin Pemandu (Guiding Block)',
            'Toilet Aksesibel',
            'Pegangan Tangan (Handrail)',
            'Parkir Khusus Disabilitas',
            'Pintu Masuk Aksesibel',
        ];

        foreach ($features as $name) {
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
                [
                    'area' => 'Kawasan Gedung Kuliah Bersama (GKB)',
                    'officer' => ['Petugas GKB Telkom', 'petugas.telkom@aksesloka.id'],
                    'locations' => [
                        [
                            'name' => 'Gedung Tokong Nanas (GKB)',
                            'location_type' => 'building',
                            'description' => 'Gedung kuliah 10 lantai dengan akses ramah kursi roda di lobi utama.',
                            'latitude' => -6.9730780,
                            'longitude' => 107.6302810,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Lift Aksesibel', 'Toilet Aksesibel'],
                        ],
                        [
                            'name' => 'Open Library (Perpustakaan Pusat)',
                            'location_type' => 'library',
                            'description' => 'Gedung perpustakaan pusat Telkom University dengan fasilitas pemandu dan pintu otomatis.',
                            'latitude' => -6.9745120,
                            'longitude' => 107.6318500,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Ubin Pemandu (Guiding Block)', 'Pintu Masuk Aksesibel'],
                        ],
                    ],
                ],
                [
                    'area' => 'Kawasan Fakultas Teknik & Informatika',
                    'officer' => ['Petugas Fakultas Telkom', 'petugas.telkom2@aksesloka.id'],
                    'locations' => [
                        [
                            'name' => 'Gedung Fakultas Informatika (Gedung Cacuk)',
                            'location_type' => 'building',
                            'description' => 'Gedung perkuliahan dan laboratorium komputasi dengan jalur akses landai dan pegangan tangan.',
                            'latitude' => -6.9692300,
                            'longitude' => 107.6284200,
                            'accessibility_status' => 'partially_accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Pegangan Tangan (Handrail)'],
                        ],
                        [
                            'name' => 'Masjid Syamsul Ulum Telkom',
                            'location_type' => 'worship_place',
                            'description' => 'Tempat ibadah kampus dengan area wudhu dan jalur ramah disabilitas.',
                            'latitude' => -6.9715000,
                            'longitude' => 107.6291000,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Toilet Aksesibel', 'Parkir Khusus Disabilitas'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($demoCampuses as $campusName => $areas) {
            $campus = Campus::where('name', $campusName)->firstOrFail();

            foreach ($areas as $areaData) {
                $area = CampusArea::updateOrCreate(
                    ['campus_id' => $campus->id, 'name' => $areaData['area']],
                    ['is_active' => true],
                );

                User::updateOrCreate(
                    ['email' => $areaData['officer'][1]],
                    [
                        'name' => $areaData['officer'][0],
                        'password' => 'Password123!',
                        'role' => 'officer',
                        'affiliation_type' => 'staff',
                        'campus_id' => $campus->id,
                        'campus_area_id' => $area->id,
                        'is_active' => true,
                    ],
                );

                foreach ($areaData['locations'] as $locData) {
                    $location = CampusLocation::updateOrCreate(
                        ['campus_area_id' => $area->id, 'name' => $locData['name']],
                        [
                            'location_type' => $locData['location_type'],
                            'description' => $locData['description'],
                            'latitude' => $locData['latitude'],
                            'longitude' => $locData['longitude'],
                            'accessibility_status' => $locData['accessibility_status'],
                            'is_active' => true,
                        ],
                    );

                    foreach ($locData['features'] as $featureName) {
                        $feature = AccessibilityFeature::where('name', $featureName)->firstOrFail();

                        LocationAccessibilityFeature::updateOrCreate(
                            [
                                'campus_location_id' => $location->id,
                                'accessibility_feature_id' => $feature->id,
                            ],
                            [
                                'availability_status' => 'available',
                                'condition' => 'good',
                                'notes' => 'Fasilitas aktif terverifikasi untuk aksesibilitas kampus.',
                                'last_checked_at' => now(),
                            ],
                        );
                    }
                }
            }
        }
    }
}

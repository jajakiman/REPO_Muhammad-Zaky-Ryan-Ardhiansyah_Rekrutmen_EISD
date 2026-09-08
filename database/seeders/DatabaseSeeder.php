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
            'Universitas Pendidikan Indonesia' => [
                [
                    'area' => 'Kawasan FPMIPA & Gymnasium',
                    'officer' => ['Petugas FPMIPA UPI', 'petugas.upi@aksesloka.id'],
                    'locations' => [
                        [
                            'name' => 'Gedung JICA FPMIPA UPI',
                            'location_type' => 'building',
                            'description' => 'Gedung perkuliahan MIPA dengan fasilitas lift dan toilet khusus.',
                            'latitude' => -6.8601234,
                            'longitude' => 107.5891234,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Lift Aksesibel', 'Toilet Aksesibel'],
                        ],
                        [
                            'name' => 'Gymnasium UPI',
                            'location_type' => 'building',
                            'description' => 'Gedung olahraga dan kegiatan mahasiswa dengan ramp dan tempat parkir khusus disabilitas.',
                            'latitude' => -6.8624000,
                            'longitude' => 107.5912000,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Pegangan Tangan (Handrail)', 'Parkir Khusus Disabilitas'],
                        ],
                    ],
                ],
                [
                    'area' => 'Kawasan Isola & Rektorat',
                    'officer' => ['Petugas Rektorat UPI', 'petugas.upi2@aksesloka.id'],
                    'locations' => [
                        [
                            'name' => 'Gedung Isola (Rektorat UPI)',
                            'location_type' => 'building',
                            'description' => 'Gedung bersejarah dan pusat rektorat UPI dengan ubin pemandu di pelataran.',
                            'latitude' => -6.8607250,
                            'longitude' => 107.5944550,
                            'accessibility_status' => 'partially_accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Ubin Pemandu (Guiding Block)'],
                        ],
                        [
                            'name' => 'Perpustakaan Pusat UPI',
                            'location_type' => 'library',
                            'description' => 'Gedung perpustakaan pusat 4 lantai dengan lift aksesibel dan pintu otomatis.',
                            'latitude' => -6.8618000,
                            'longitude' => 107.5935000,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Lift Aksesibel', 'Pintu Masuk Aksesibel'],
                        ],
                    ],
                ],
            ],
            'Universitas Teknologi Bandung' => [
                [
                    'area' => 'Kawasan Kampus Utama',
                    'officer' => ['Petugas Kampus Utama UTB', 'petugas.utb@aksesloka.id'],
                    'locations' => [
                        [
                            'name' => 'Gedung Magnesit (Rektorat)',
                            'location_type' => 'building',
                            'description' => 'Gedung administrasi dan rektorat UTB dengan fasilitas parkir dan pintu masuk aksesibel.',
                            'latitude' => -6.8634000,
                            'longitude' => 107.6092000,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Pintu Masuk Aksesibel', 'Parkir Khusus Disabilitas'],
                        ],
                        [
                            'name' => 'Gedung Kuliah Terpadu UTB',
                            'location_type' => 'building',
                            'description' => 'Gedung perkuliahan umum dengan toilet aksesibel dan pegangan tangan tangga.',
                            'latitude' => -6.8641000,
                            'longitude' => 107.6105000,
                            'accessibility_status' => 'accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Toilet Aksesibel', 'Pegangan Tangan (Handrail)'],
                        ],
                    ],
                ],
                [
                    'area' => 'Kawasan Laboratorium & Workshop',
                    'officer' => ['Petugas Laboratorium UTB', 'petugas.utb2@aksesloka.id'],
                    'locations' => [
                        [
                            'name' => 'Laboratorium Rekayasa & Workshop',
                            'location_type' => 'building',
                            'description' => 'Pusat praktikum dan workshop teknik dengan ramp di pintu masuk.',
                            'latitude' => -6.8628000,
                            'longitude' => 107.6080000,
                            'accessibility_status' => 'partially_accessible',
                            'features' => ['Jalur Landai (Ramp)', 'Pegangan Tangan (Handrail)'],
                        ],
                        [
                            'name' => 'Area Parkir & Plaza Barat',
                            'location_type' => 'parking',
                            'description' => 'Kawasan parkir terpadu dengan slot khusus disabilitas dan ubin pemandu menuju gedung.',
                            'latitude' => -6.8639000,
                            'longitude' => 107.6098000,
                            'accessibility_status' => 'accessible',
                            'features' => ['Parkir Khusus Disabilitas', 'Ubin Pemandu (Guiding Block)'],
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

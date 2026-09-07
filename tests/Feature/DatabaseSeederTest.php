<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_demo_accounts_areas_locations_and_facilities_idempotently(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@aksesloka.id')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('Password123!', $admin->password));

        foreach (['petugas.telkom@aksesloka.id', 'petugas.upi@aksesloka.id', 'petugas.utb@aksesloka.id'] as $email) {
            $officer = User::where('email', $email)->firstOrFail();
            $this->assertSame('officer', $officer->role);
            $this->assertNotNull($officer->campus_id);
            $this->assertNotNull($officer->campus_area_id);
            $this->assertTrue(Hash::check('Password123!', $officer->password));
        }

        $this->assertDatabaseCount('campus_areas', 3);
        $this->assertDatabaseCount('campus_locations', 3);
        $this->assertDatabaseCount('location_accessibility_features', 9);
        $this->assertDatabaseHas('campus_locations', [
            'name' => 'Gedung Tokong Nanas',
            'location_type' => 'building',
            'accessibility_status' => 'accessible',
        ]);
        $this->assertDatabaseHas('campus_locations', ['name' => 'Gedung Isola']);
        $this->assertDatabaseHas('campus_locations', ['name' => 'Gedung Magnesit']);
        $this->assertDatabaseHas('location_accessibility_features', [
            'availability_status' => 'available',
            'condition' => 'good',
        ]);
    }
}

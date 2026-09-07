<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('campus_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['campus_id', 'name']);
        });

        Schema::create('campus_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_area_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->enum('location_type', ['building', 'library', 'worship_place', 'green_space', 'parking', 'pedestrian_area', 'shuttle_stop']);
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->enum('accessibility_status', ['accessible', 'partially_accessible', 'inaccessible', 'not_assessed'])->default('not_assessed')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['campus_area_id', 'name']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['reporter', 'officer', 'admin'])->default('reporter')->index();
            $table->enum('affiliation_type', ['student', 'lecturer', 'staff', 'visitor'])->default('visitor');
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campus_area_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_area_id');
            $table->dropConstrainedForeignId('campus_id');
            $table->dropColumn(['role', 'affiliation_type', 'is_active']);
        });
        Schema::dropIfExists('campus_locations');
        Schema::dropIfExists('campus_areas');
        Schema::dropIfExists('campuses');
    }
};

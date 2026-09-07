<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessibility_features', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('issue_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('location_accessibility_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_location_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('accessibility_feature_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('availability_status', ['available', 'unavailable']);
            $table->enum('condition', ['good', 'needs_repair', 'blocked', 'broken']);
            $table->text('notes')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
            $table->unique(['campus_location_id', 'accessibility_feature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_accessibility_features');
        Schema::dropIfExists('issue_categories');
        Schema::dropIfExists('accessibility_features');
    }
};

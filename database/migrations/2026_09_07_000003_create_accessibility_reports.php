<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessibility_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('officer_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('location_accessibility_feature_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('issue_category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->enum('status', ['submitted', 'verified', 'in_progress', 'resolved', 'rejected', 'cancelled'])->default('submitted')->index();
            $table->enum('priority', ['low', 'medium', 'high'])->nullable()->index();
            $table->text('rejection_reason')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->string('resolution_photo_path')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('handling_started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['reporter_id', 'created_at']);
            $table->index(['officer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessibility_reports');
    }
};

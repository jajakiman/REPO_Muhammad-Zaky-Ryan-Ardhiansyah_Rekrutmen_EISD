<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessibilityFeature extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function campusLocations(): BelongsToMany
    {
        return $this->belongsToMany(CampusLocation::class, 'location_accessibility_features')
            ->withPivot(['id', 'availability_status', 'condition', 'notes', 'last_checked_at'])
            ->withTimestamps();
    }

    public function locationAccessibilityFeatures(): HasMany
    {
        return $this->hasMany(LocationAccessibilityFeature::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}

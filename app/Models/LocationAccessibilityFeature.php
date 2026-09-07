<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationAccessibilityFeature extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['last_checked_at' => 'datetime'];
    }

    public function campusLocation(): BelongsTo
    {
        return $this->belongsTo(CampusLocation::class);
    }

    public function accessibilityFeature(): BelongsTo
    {
        return $this->belongsTo(AccessibilityFeature::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AccessibilityReport::class);
    }
}

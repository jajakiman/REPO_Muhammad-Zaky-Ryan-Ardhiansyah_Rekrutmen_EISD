<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusArea extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(CampusLocation::class);
    }

    public function officers(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'officer');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}

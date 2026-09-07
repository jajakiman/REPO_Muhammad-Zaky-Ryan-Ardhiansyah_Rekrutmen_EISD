<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessibilityReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'handling_started_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function issueCategory(): BelongsTo
    {
        return $this->belongsTo(IssueCategory::class);
    }

    public function locationAccessibilityFeature(): BelongsTo
    {
        return $this->belongsTo(LocationAccessibilityFeature::class);
    }
}

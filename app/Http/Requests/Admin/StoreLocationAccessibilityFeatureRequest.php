<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationAccessibilityFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'accessibility_feature_id' => [
                'required',
                Rule::exists('accessibility_features', 'id')->where('is_active', true),
                Rule::unique('location_accessibility_features')->where('campus_location_id', $this->route('location')->id),
            ],
            'availability_status' => ['required', Rule::in(['available', 'unavailable'])],
            'condition' => ['required', Rule::in(['good', 'needs_repair', 'blocked', 'broken'])],
            'notes' => ['nullable', 'string'],
            'last_checked_at' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return ['accessibility_feature_id' => 'fasilitas', 'availability_status' => 'ketersediaan', 'condition' => 'kondisi', 'notes' => 'catatan', 'last_checked_at' => 'waktu pemeriksaan'];
    }
}

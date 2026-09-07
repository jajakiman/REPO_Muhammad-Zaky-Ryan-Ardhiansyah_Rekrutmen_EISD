<?php

namespace App\Http\Requests\Reporter;

use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'reporter';
    }

    public function rules(): array
    {
        return [
            'location_accessibility_feature_id' => [
                'required',
                'exists:location_accessibility_features,id',
                function ($attribute, $value, $fail) {
                    $laf = LocationAccessibilityFeature::with(['campusLocation.campusArea.campus', 'accessibilityFeature'])->find($value);
                    if (! $laf || ! $laf->accessibilityFeature?->is_active ||
                        ! $laf->campusLocation?->is_active ||
                        ! $laf->campusLocation?->campusArea?->is_active ||
                        ! $laf->campusLocation?->campusArea?->campus?->is_active) {
                        $fail('Fasilitas pada lokasi ini tidak aktif atau tidak dapat dilaporkan.');
                    }
                },
            ],
            'issue_category_id' => [
                'required',
                Rule::exists('issue_categories', 'id')->where('is_active', true),
            ],
            'description' => ['required', 'string', 'min:5', 'max:2000'],
            'photo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'location_accessibility_feature_id' => 'fasilitas lokasi',
            'issue_category_id' => 'kategori masalah',
            'description' => 'deskripsi masalah',
            'photo' => 'foto laporan',
        ];
    }
}

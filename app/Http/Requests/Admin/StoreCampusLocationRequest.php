<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampusLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $area = $this->route('area');

        return [
            'area' => [fn ($attribute, $value, $fail) => ($area->is_active && $area->campus->is_active) ?: $fail('Kampus dan area harus aktif.')],
            'name' => ['required', 'string', 'max:255', Rule::unique('campus_locations')->where('campus_area_id', $area->id)],
            'location_type' => ['required', Rule::in(['building', 'library', 'worship_place', 'green_space', 'parking', 'pedestrian_area', 'shuttle_stop'])],
            'description' => ['nullable', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accessibility_status' => ['required', Rule::in(['accessible', 'partially_accessible', 'inaccessible', 'not_assessed'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['area' => $this->route('area')->id]);
    }

    public function attributes(): array
    {
        return ['area' => 'area', 'name' => 'nama lokasi', 'location_type' => 'tipe lokasi', 'description' => 'deskripsi', 'latitude' => 'latitude', 'longitude' => 'longitude', 'accessibility_status' => 'status aksesibilitas'];
    }
}

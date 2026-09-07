<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampusLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('campus_locations')->where('campus_area_id', $this->route('area')->id)->ignore($this->route('location'))],
            'location_type' => ['required', Rule::in(['building', 'library', 'worship_place', 'green_space', 'parking', 'pedestrian_area', 'shuttle_stop'])],
            'description' => ['nullable', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accessibility_status' => ['required', Rule::in(['accessible', 'partially_accessible', 'inaccessible', 'not_assessed'])],
            'is_active' => ['required', 'boolean'],
        ];
    }
}

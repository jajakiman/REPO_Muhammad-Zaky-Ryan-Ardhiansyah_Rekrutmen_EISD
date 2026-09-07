<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationAccessibilityFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'availability_status' => ['required', Rule::in(['available', 'unavailable'])],
            'condition' => ['required', Rule::in(['good', 'needs_repair', 'blocked', 'broken'])],
            'notes' => ['nullable', 'string'],
            'last_checked_at' => ['nullable', 'date'],
        ];
    }
}

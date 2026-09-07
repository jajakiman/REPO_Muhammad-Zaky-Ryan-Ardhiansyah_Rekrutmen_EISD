<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccessibilityFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255', Rule::unique('accessibility_features')->ignore($this->route('feature'))], 'is_active' => ['required', 'boolean']];
    }
}

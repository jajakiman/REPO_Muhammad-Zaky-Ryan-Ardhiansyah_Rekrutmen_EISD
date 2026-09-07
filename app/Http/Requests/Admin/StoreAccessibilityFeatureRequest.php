<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccessibilityFeatureRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'admin'; }
    public function rules(): array { return ['name' => ['required', 'string', 'max:255', 'unique:accessibility_features,name']]; }
    public function attributes(): array { return ['name' => 'nama fasilitas']; }
}

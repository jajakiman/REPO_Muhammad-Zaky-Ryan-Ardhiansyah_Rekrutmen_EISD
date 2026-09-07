<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIssueCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255', Rule::unique('issue_categories')->ignore($this->route('issue_category'))], 'is_active' => ['required', 'boolean']];
    }
}

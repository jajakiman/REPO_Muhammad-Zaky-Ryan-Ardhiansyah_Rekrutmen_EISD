<?php

namespace App\Http\Requests\Reporter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'reporter';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'affiliation_type' => ['required', Rule::in(['student', 'lecturer', 'staff', 'visitor'])],
            'campus_id' => [
                Rule::requiredIf(fn () => in_array($this->input('affiliation_type'), ['student', 'lecturer', 'staff'], true)),
                'nullable',
                Rule::exists('campuses', 'id')->where('is_active', true),
            ],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama', 'affiliation_type' => 'afiliasi', 'campus_id' => 'kampus'];
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'affiliation_type' => 'afiliasi',
            'campus_id' => 'kampus',
        ];
    }
}

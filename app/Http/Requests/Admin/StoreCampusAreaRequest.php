<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampusAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'campus' => [fn ($attribute, $value, $fail) => $this->route('campus')->is_active ?: $fail('Kampus harus aktif.')],
            'name' => ['required', 'string', 'max:255', Rule::unique('campus_areas')->where('campus_id', $this->route('campus')->id)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['campus' => $this->route('campus')->id]);
    }

    public function attributes(): array
    {
        return ['campus' => 'kampus', 'name' => 'nama area'];
    }
}

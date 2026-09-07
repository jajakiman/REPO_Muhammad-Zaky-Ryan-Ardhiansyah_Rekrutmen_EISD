<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:campuses,name'],
            'address' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama kampus', 'address' => 'alamat'];
    }
}

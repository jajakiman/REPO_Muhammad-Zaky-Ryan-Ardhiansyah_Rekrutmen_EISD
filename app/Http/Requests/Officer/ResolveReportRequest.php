<?php

namespace App\Http\Requests\Officer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'officer';
    }

    public function rules(): array
    {
        return [
            'resolution_notes' => ['required', 'string', 'min:5', 'max:2000'],
            'condition' => ['required', Rule::in(['good', 'needs_repair', 'blocked', 'broken'])],
            'resolution_photo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'resolution_notes' => 'catatan hasil penanganan',
            'condition' => 'kondisi fasilitas terkini',
            'resolution_photo' => 'foto hasil penanganan',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\CampusArea;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfficerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'campus_area_id' => [
                'required',
                'exists:campus_areas,id',
                function ($attribute, $value, $fail) {
                    $area = CampusArea::with('campus')->find($value);
                    if (! $area || ! $area->is_active || ! $area->campus?->is_active) {
                        $fail('Area dan kampus yang dipilih harus berstatus aktif.');
                    }
                },
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama petugas',
            'email' => 'alamat email',
            'password' => 'kata sandi',
            'campus_area_id' => 'area tugas',
        ];
    }
}

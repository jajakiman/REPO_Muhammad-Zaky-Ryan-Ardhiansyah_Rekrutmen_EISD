<?php

namespace App\Http\Requests\Admin;

use App\Models\CampusArea;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'campus_area_id' => [
                'required',
                'exists:campus_areas,id',
                function ($attribute, $value, $fail) {
                    $area = CampusArea::with('campus')->find($value);
                    if (! $area || ! $area->is_active || ! $area->campus?->is_active) {
                        $fail('Area dan kampus yang dipilih harus berstatus aktif.');
                    } elseif ($area->campus_id !== $this->route('officer')?->campus_id) {
                        $fail('Area tugas harus berada dalam kampus petugas saat ini.');
                    }
                },
            ],
            'is_active' => ['required', 'in:0,1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama petugas',
            'campus_area_id' => 'area tugas',
            'is_active' => 'status akun',
        ];
    }
}

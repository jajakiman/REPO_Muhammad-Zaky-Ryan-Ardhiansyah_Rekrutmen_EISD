<?php

namespace App\Http\Requests\Officer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'officer';
    }

    public function rules(): array
    {
        return [
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'priority' => 'prioritas penanganan',
        ];
    }
}

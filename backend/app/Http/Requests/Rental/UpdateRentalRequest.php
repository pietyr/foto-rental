<?php

namespace App\Http\Requests\Rental;

use App\Enums\RentalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(RentalStatus::class)],
            'end_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Podaj status wypożyczenia.',
        ];
    }
}

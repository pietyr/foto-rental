<?php

namespace App\Http\Requests\Equipment;

use App\Enums\EquipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'price_per_day' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::enum(EquipmentStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Wybrana kategoria nie istnieje.',
            'price_per_day.min' => 'Cena nie może być ujemna.',
        ];
    }
}

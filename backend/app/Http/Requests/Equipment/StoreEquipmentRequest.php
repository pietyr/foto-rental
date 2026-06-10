<?php

namespace App\Http\Requests\Equipment;

use App\Enums\EquipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in([
                EquipmentStatus::Available->value,
                EquipmentStatus::Maintenance->value,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Wybierz kategorię.',
            'category_id.exists' => 'Wybrana kategoria nie istnieje.',
            'name.required' => 'Podaj nazwę sprzętu.',
            'price_per_day.required' => 'Podaj cenę za dzień.',
            'price_per_day.min' => 'Cena nie może być ujemna.',
        ];
    }
}

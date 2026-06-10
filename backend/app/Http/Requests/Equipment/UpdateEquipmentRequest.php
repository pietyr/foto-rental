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
            'status' => ['sometimes', Rule::in([
                EquipmentStatus::Available->value,
                EquipmentStatus::Maintenance->value,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Wybrana kategoria nie istnieje.',
            'price_per_day.min' => 'Cena nie może być ujemna.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->has('status')) {
                return;
            }

            $equipment = $this->route('equipment');

            if ($equipment->status === EquipmentStatus::Rented) {
                $validator->errors()->add(
                    'status',
                    'Sprzęt jest wypożyczony — status zmieniasz w panelu wypożyczeń.',
                );
            }
        });
    }
}

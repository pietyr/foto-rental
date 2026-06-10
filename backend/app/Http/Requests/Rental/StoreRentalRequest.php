<?php

namespace App\Http\Requests\Rental;

use App\Enums\EquipmentStatus;
use App\Models\Equipment;
use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipment_id' => ['required', 'exists:equipment,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.required' => 'Wybierz sprzęt.',
            'equipment_id.exists' => 'Wybrany sprzęt nie istnieje.',
            'start_date.required' => 'Podaj datę rozpoczęcia.',
            'start_date.after_or_equal' => 'Data rozpoczęcia nie może być w przeszłości.',
            'end_date.after_or_equal' => 'Data zakończenia musi być po dacie rozpoczęcia.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $equipmentId = $this->input('equipment_id');
            if (! $equipmentId) {
                return;
            }

            $equipment = Equipment::find($equipmentId);
            if ($equipment && $equipment->status !== EquipmentStatus::Available) {
                $validator->errors()->add('equipment_id', 'Ten sprzęt nie jest dostępny.');
            }
        });
    }
}

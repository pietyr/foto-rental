<?php

namespace App\Http\Requests\Rental;

use App\Enums\EquipmentStatus;
use App\Enums\RentalStatus;
use App\Models\Equipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'equipment_id' => ['required', 'exists:equipment,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::enum(RentalStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Wybierz użytkownika.',
            'user_id.exists' => 'Wybrany użytkownik nie istnieje.',
            'equipment_id.required' => 'Wybierz sprzęt.',
            'equipment_id.exists' => 'Wybrany sprzęt nie istnieje.',
            'start_date.required' => 'Podaj datę rozpoczęcia.',
            'end_date.after_or_equal' => 'Data zakończenia musi być po dacie rozpoczęcia.',
            'status.required' => 'Podaj status wypożyczenia.',
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

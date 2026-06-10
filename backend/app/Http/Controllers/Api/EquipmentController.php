<?php

namespace App\Http\Controllers\Api;

use App\Enums\EquipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\StoreEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Equipment::query()->with('category')->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->boolean('available_only')) {
            $query->where('status', EquipmentStatus::Available);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(Equipment $equipment): JsonResponse
    {
        $equipment->load('category');

        return response()->json(['data' => $equipment]);
    }

    public function store(StoreEquipmentRequest $request): JsonResponse
    {
        $equipment = Equipment::create($request->validated());
        $equipment->load('category');

        return response()->json(['data' => $equipment], 201);
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): JsonResponse
    {
        $equipment->update($request->validated());
        $equipment->load('category');

        return response()->json(['data' => $equipment]);
    }

    public function destroy(Equipment $equipment): JsonResponse
    {
        if ($equipment->rentals()->whereIn('status', ['pending', 'active'])->exists()) {
            return response()->json([
                'message' => 'Nie można usunąć sprzętu z aktywnymi wypożyczeniami.',
            ], 422);
        }

        $equipment->delete();

        return response()->json(['message' => 'Sprzęt usunięty.']);
    }
}

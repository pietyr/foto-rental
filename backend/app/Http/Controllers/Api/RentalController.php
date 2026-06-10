<?php

namespace App\Http\Controllers\Api;

use App\Enums\EquipmentStatus;
use App\Enums\RentalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rental\StoreRentalRequest;
use App\Http\Requests\Rental\UpdateRentalRequest;
use App\Models\Rental;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Rental::query()
            ->with(['equipment.category', 'user'])
            ->latest();

        if ($request->boolean('all')) {
            if (! $request->user()->isAdmin()) {
                return response()->json(['message' => 'Brak uprawnień.'], 403);
            }
        } else {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(Request $request, Rental $rental): JsonResponse
    {
        if (! $request->user()->isAdmin() && $rental->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Brak uprawnień.'], 403);
        }

        $rental->load(['equipment.category', 'user']);

        return response()->json(['data' => $rental]);
    }

    public function store(StoreRentalRequest $request): JsonResponse
    {
        $rental = Rental::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => RentalStatus::Pending,
        ]);

        $rental->load(['equipment.category', 'user']);

        return response()->json(['data' => $rental], 201);
    }

    public function update(UpdateRentalRequest $request, Rental $rental): JsonResponse
    {
        $status = RentalStatus::from($request->validated('status'));

        DB::transaction(function () use ($request, $rental, $status) {
            $rental->update([
                'status' => $status,
                'end_date' => $request->validated('end_date') ?? $rental->end_date,
            ]);

            $equipment = $rental->equipment;

            match ($status) {
                RentalStatus::Active => $equipment->update(['status' => EquipmentStatus::Rented]),
                RentalStatus::Completed, RentalStatus::Cancelled => $equipment->update(['status' => EquipmentStatus::Available]),
                RentalStatus::Pending => null,
            };
        });

        $rental->refresh()->load(['equipment.category', 'user']);

        return response()->json(['data' => $rental]);
    }
}

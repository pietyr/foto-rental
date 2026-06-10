<?php

namespace Database\Seeders;

use App\Enums\EquipmentStatus;
use App\Enums\RentalStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@foto-rental.test',
            'password' => 'password',
            'role' => UserRole::Admin,
        ]);

        $jan = User::factory()->create([
            'name' => 'Jan Kowalski',
            'email' => 'user@foto-rental.test',
            'password' => 'password',
            'role' => UserRole::User,
        ]);

        $anna = User::factory()->create([
            'name' => 'Anna Nowak',
            'email' => 'anna@foto-rental.test',
            'password' => 'password',
            'role' => UserRole::User,
        ]);

        $cameras = Category::create(['name' => 'Aparaty']);
        $lenses = Category::create(['name' => 'Obiektywy']);
        $tripods = Category::create(['name' => 'Statywy']);

        $equipment = [
            [
                'category_id' => $cameras->id,
                'name' => 'Canon EOS R6',
                'description' => 'Bezlusterkowiec full frame, 20 MP.',
                'brand' => 'Canon',
                'model' => 'EOS R6',
                'price_per_day' => 120.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $cameras->id,
                'name' => 'Sony A7 III',
                'description' => 'Aparat do zdjęć i wideo.',
                'brand' => 'Sony',
                'model' => 'A7 III',
                'price_per_day' => 100.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $cameras->id,
                'name' => 'Nikon Z6 II',
                'description' => 'Hybryda z podwójnym slotem na karty.',
                'brand' => 'Nikon',
                'model' => 'Z6 II',
                'price_per_day' => 110.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $cameras->id,
                'name' => 'Fujifilm X-T5',
                'description' => 'Kompaktowy aparat APS-C z klasycznym wyglądem.',
                'brand' => 'Fujifilm',
                'model' => 'X-T5',
                'price_per_day' => 90.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $lenses->id,
                'name' => 'Sigma 24-70mm f/2.8',
                'description' => 'Uniwersalny zoom do reportażu.',
                'brand' => 'Sigma',
                'model' => '24-70mm Art',
                'price_per_day' => 60.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $lenses->id,
                'name' => 'Canon RF 50mm f/1.8',
                'description' => 'Lekki obiektyw stałoogniskowy.',
                'brand' => 'Canon',
                'model' => 'RF 50mm STM',
                'price_per_day' => 25.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $lenses->id,
                'name' => 'Sony FE 85mm f/1.8',
                'description' => 'Portretowy obiektyw z mocnym bokeh.',
                'brand' => 'Sony',
                'model' => 'FE 85mm',
                'price_per_day' => 45.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $lenses->id,
                'name' => 'Tamron 70-180mm f/2.8',
                'description' => 'Telezoom do zdjęć sportowych i eventów.',
                'brand' => 'Tamron',
                'model' => '70-180mm Di III',
                'price_per_day' => 55.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $tripods->id,
                'name' => 'Manfrotto MT055',
                'description' => 'Statyw studyjny z wysięgnikiem.',
                'brand' => 'Manfrotto',
                'model' => 'MT055',
                'price_per_day' => 25.00,
                'status' => EquipmentStatus::Maintenance,
            ],
            [
                'category_id' => $tripods->id,
                'name' => 'Gitzo GT3543XLS',
                'description' => 'Lekki statyw węglowy do pracy w terenie.',
                'brand' => 'Gitzo',
                'model' => 'GT3543XLS',
                'price_per_day' => 40.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $tripods->id,
                'name' => 'Benro TMA47AXL',
                'description' => 'Stabilny statyw do ciężkich korpusów.',
                'brand' => 'Benro',
                'model' => 'TMA47AXL',
                'price_per_day' => 30.00,
                'status' => EquipmentStatus::Available,
            ],
            [
                'category_id' => $tripods->id,
                'name' => 'Peak Design Travel Tripod',
                'description' => 'Kompaktowy statyw podróżny.',
                'brand' => 'Peak Design',
                'model' => 'Travel Tripod',
                'price_per_day' => 35.00,
                'status' => EquipmentStatus::Available,
            ],
        ];

        $createdEquipment = collect($equipment)->map(
            fn (array $item) => Equipment::create($item),
        );

        $fujifilm = $createdEquipment->firstWhere('name', 'Fujifilm X-T5');
        $sony = $createdEquipment->firstWhere('name', 'Sony A7 III');

        Rental::create([
            'user_id' => $jan->id,
            'equipment_id' => $fujifilm->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => RentalStatus::Active,
        ]);
        $fujifilm->update(['status' => EquipmentStatus::Rented]);

        Rental::create([
            'user_id' => $anna->id,
            'equipment_id' => $sony->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'status' => RentalStatus::Pending,
        ]);
    }
}

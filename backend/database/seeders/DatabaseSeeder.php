<?php

namespace Database\Seeders;

use App\Enums\EquipmentStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Equipment;
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

        User::factory()->create([
            'name' => 'Jan Kowalski',
            'email' => 'user@foto-rental.test',
            'password' => 'password',
            'role' => UserRole::User,
        ]);

        $cameras = Category::create(['name' => 'Aparaty']);
        $lenses = Category::create(['name' => 'Obiektywy']);
        $tripods = Category::create(['name' => 'Statywy']);

        Equipment::create([
            'category_id' => $cameras->id,
            'name' => 'Canon EOS R6',
            'description' => 'Bezlusterkowiec full frame, 20 MP.',
            'brand' => 'Canon',
            'model' => 'EOS R6',
            'price_per_day' => 120.00,
            'status' => EquipmentStatus::Available,
        ]);

        Equipment::create([
            'category_id' => $cameras->id,
            'name' => 'Sony A7 III',
            'description' => 'Aparat do zdjęć i wideo.',
            'brand' => 'Sony',
            'model' => 'A7 III',
            'price_per_day' => 100.00,
            'status' => EquipmentStatus::Available,
        ]);

        Equipment::create([
            'category_id' => $lenses->id,
            'name' => 'Sigma 24-70mm f/2.8',
            'brand' => 'Sigma',
            'model' => '24-70mm Art',
            'price_per_day' => 60.00,
            'status' => EquipmentStatus::Available,
        ]);

        Equipment::create([
            'category_id' => $tripods->id,
            'name' => 'Manfrotto MT055',
            'brand' => 'Manfrotto',
            'price_per_day' => 25.00,
            'status' => EquipmentStatus::Maintenance,
        ]);
    }
}

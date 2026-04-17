<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Service;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ FIX: is_active => true wajib disertakan eksplisit
        User::firstOrCreate(
            ['email' => 'admin@laundrypro.id'], 
            [
                'name'      => 'Admin LaundryPro',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'ceo@laundrypro.id'], 
            [
                'name'      => 'Lalo Salamanca',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'budi@laundrypro.id'], 
            [
                'name'      => 'Budiono Siregar',
                'password'  => Hash::make('password'),
                'role'      => 'karyawan',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'siti@laundrypro.id'], 
            [
                'name'      => 'Siti Rahayu',
                'password'  => Hash::make('password'),
                'role'      => 'karyawan',
                'is_active' => true,
            ]
        );

        // Services
        $services = [
            ['name' => 'Cuci Saja',            'type' => 'per_kg',   'price' => 5000,  'description' => 'Layanan cuci standar per kilogram'],
            ['name' => 'Cuci + Gosok',          'type' => 'per_kg',   'price' => 8000,  'description' => 'Layanan cuci dan setrika per kilogram'],
            ['name' => 'Cuci + Gosok Express',  'type' => 'per_kg',   'price' => 12000, 'description' => 'Layanan cuci dan setrika cepat (selesai hari ini)'],
            ['name' => 'Seprai',                'type' => 'per_item', 'price' => 15000, 'description' => 'Cuci + gosok per lembar seprai'],
            ['name' => 'Selimut',               'type' => 'per_item', 'price' => 20000, 'description' => 'Cuci + gosok per lembar selimut'],
            ['name' => 'Gordyn / Korden',       'type' => 'per_item', 'price' => 25000, 'description' => 'Cuci per lembar gordyn'],
            ['name' => 'Boneka Kecil',          'type' => 'per_item', 'price' => 10000, 'description' => 'Cuci boneka ukuran kecil'],
            ['name' => 'Boneka Besar',          'type' => 'per_item', 'price' => 20000, 'description' => 'Cuci boneka ukuran besar'],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}

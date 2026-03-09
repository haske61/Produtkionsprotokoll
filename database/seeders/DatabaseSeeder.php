<?php

namespace Database\Seeders;

use App\Models\ProductionLine;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Hasan Keskin',
            'email' => 'hasan-keskin@outlook.de',
            'password' => bcrypt('password'),
        ]);

        ProductionLine::firstOrCreate(['name' => 'Linie 1']);
        ProductionLine::firstOrCreate(['name' => 'Linie 2']);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marca;

class MarcasSeeder extends Seeder
{
    public function run(): void
    {
        Marca::insert([
            ['nombre' => 'Samsung'],
            ['nombre' => 'LG'],
            ['nombre' => 'Sony'],
            ['nombre' => 'Xiaomi'],
        ]);
    }
}

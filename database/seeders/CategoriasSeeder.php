<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::insert([
            ['nombre' => 'Electrodomésticos'],
            ['nombre' => 'Tecnología'],
            ['nombre' => 'Comida'],
        ]);
    }
}


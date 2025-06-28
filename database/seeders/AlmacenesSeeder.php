<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Almacen;

class AlmacenesSeeder extends Seeder
{
    public function run(): void
    {
        Almacen::insert([
            ['nombre' => 'Central', 'ubicacion' => 'Calle 123, Bogotá'],
            ['nombre' => 'Sucursal Norte', 'ubicacion' => 'Carrera 45, Medellín'],
            ['nombre' => 'Sucursal Sur', 'ubicacion' => 'Av. 68, Cali'],
        ]);
    }
}
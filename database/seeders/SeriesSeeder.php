<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Serie;
use App\Models\Producto;
use App\Models\Almacen;

class SeriesSeeder extends Seeder
{
    public function run(): void
    {
        $producto = Producto::first();
        $almacen = Almacen::first();

        Serie::insert([
            [
                'producto_id' => $producto->id,
                'almacen_id' => $almacen->id,
                'numero_serie' => 'SERIE123456',
                'estado' => 'disponible',
            ],
            [
                'producto_id' => $producto->id,
                'almacen_id' => $almacen->id,
                'numero_serie' => 'SERIE123457',
                'estado' => 'vendida',
            ],
        ]);
    }
}

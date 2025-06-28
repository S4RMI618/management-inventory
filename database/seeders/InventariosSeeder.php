<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Serie;

class InventariosSeeder extends Seeder
{
    public function run(): void
    {
        $almacen = Almacen::first();
        $productos = Producto::all();

        foreach ($productos as $producto) {
            // Contar cuántas series disponibles hay para el producto en el almacén
            $cantidadSeries = Serie::where('producto_id', $producto->id)
                                    ->where('almacen_id', $almacen->id)
                                    ->count();

            // Registrar inventario solo si hay series asociadas
            if ($cantidadSeries > 0) {
                Inventario::updateOrCreate(
                    [
                        'producto_id' => $producto->id,
                        'almacen_id' => $almacen->id,
                    ],
                    [
                        'cantidad' => $cantidadSeries,
                    ]
                );
            }
        }
    }
}



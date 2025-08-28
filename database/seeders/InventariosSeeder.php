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
        if (!$almacen) {
            echo "No hay almacenes para registrar inventarios.\n";
            return;
        }

        $productos = Producto::all();

        foreach ($productos as $producto) {
            $cantidadSeries = Serie::where('producto_id', $producto->id)
                ->where('almacen_id', $almacen->id)
                ->where('estado', 'disponible') // evita contar las vendidas
                ->count();

            if ($cantidadSeries > 0) {
                $costoUnitario = (float) ($producto->precio_costo ?? 0);

                // ===== Elige UNA de las dos opciones =====
                // Opción A: guardar costo UNITARIO
                $costo = $costoUnitario;

                // Opción B: guardar costo TOTAL del inventario
                // $costo = $costoUnitario * $cantidadSeries;

                Inventario::updateOrCreate(
                    [
                        'producto_id' => $producto->id,
                        'almacen_id'  => $almacen->id,
                    ],
                    [
                        'cantidad' => $cantidadSeries,
                        'costo'    => $costo,
                    ]
                );
            }
        }
    }
}

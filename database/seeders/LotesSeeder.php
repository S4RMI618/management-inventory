<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lote;
use App\Models\Serie;
use App\Models\Producto;
use App\Models\Almacen;

class LotesSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();
        $almacen = Almacen::first();
        $seriesGeneradas = 0;

        foreach ($productos as $producto) {
            $categoria = optional($producto->categoria)->nombre;
            $requiereVencimiento = strtolower(optional($producto->categoria)->nombre ?? '') === 'comida';

            // Definimos un número fijo de unidades por lote (ej. 5 unidades)
            $cantidad = 5;

            $lote = Lote::create([
                'producto_id' => $producto->id,
                'numero_lote' => 'L-' . strtoupper(substr($producto->codigo, -3)) . '-' . rand(1000, 9999),
                'fecha_fabricacion' => now()->subMonths(rand(1, 12)),
                'fecha_vencimiento' => $requiereVencimiento ? now()->addYears(2) : null,
                'tiene_invima' => $producto->tiene_invima,
            ]);

            // Generamos series para cada unidad del lote
            for ($i = 1; $i <= $cantidad; $i++) {
                Serie::create([
                    'producto_id' => $producto->id,
                    'almacen_id' => $almacen->id,
                    'numero_serie' => 'SER-' . $producto->id . '-' . $lote->id . '-' . $i,
                    'estado' => 'disponible',
                ]);
                $seriesGeneradas++;
            }
        }

        echo "Lotes y $seriesGeneradas series generadas correctamente.\n";
    }
}

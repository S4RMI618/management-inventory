<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Lote;
use App\Models\Serie;

class SeriesSeeder extends Seeder
{
    public function run(): void
    {
        $almacen = Almacen::first();
        if (!$almacen) {
            echo "No hay almacenes; no se pueden crear series.\n";
            return;
        }

        // Toma algunos productos existentes (o quita take(3) para usar todos)
        $productos = Producto::take(3)->get();
        if ($productos->isEmpty()) {
            echo "No hay productos; no se pueden crear series.\n";
            return;
        }

        foreach ($productos as $producto) {
            // 2 lotes por producto
            foreach (range(1, 2) as $numLote) {
                $lote = Lote::create([
                    'producto_id'        => $producto->id,
                    'numero_lote'        => 'LOTE' . $producto->id . $numLote,
                    'fecha_fabricacion'  => now()->subMonths(rand(1, 5)),
                    'fecha_vencimiento'  => now()->addMonths(rand(6, 12)),
                    'tiene_invima'       => (bool) $producto->tiene_invima,
                ]);

                // 5 series por lote
                foreach (range(1, 5) as $numSerie) {
                    Serie::create([
                        'producto_id'  => $producto->id,
                        'almacen_id'   => $almacen->id,
                        'numero_serie' => 'SERIE-' . $producto->id . $numLote . sprintf('%03d', $numSerie),
                        'estado'       => ($numSerie === 1) ? 'vendida' : 'disponible',
                        'lote_id'      => $lote->id,
                    ]);
                }
            }
        }
    }
}

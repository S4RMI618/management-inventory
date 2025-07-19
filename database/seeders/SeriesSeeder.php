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

        // Crea 3 productos de ejemplo
        foreach (range(1, 3) as $numProd) {
            $producto = Producto::create([
                'codigo' => 'PROD00' . $numProd,
                'nombre' => 'Producto ' . $numProd,
                // Agrega los demás campos obligatorios según tu modelo...
            ]);

            // Cada producto tendrá 2 lotes
            foreach (range(1, 2) as $numLote) {
                $lote = Lote::create([
                    'producto_id' => $producto->id,
                    'numero_lote' => 'LOTE' . $numProd . $numLote,
                    'fecha_fabricacion' => now()->subMonths(rand(1, 5)),
                    'fecha_vencimiento' => now()->addMonths(rand(6, 12)),
                    'tiene_invima' => false,
                ]);

                // Cada lote tendrá 5 series
                foreach (range(1, 5) as $numSerie) {
                    Serie::create([
                        'producto_id' => $producto->id,
                        'almacen_id' => $almacen->id,
                        'numero_serie' => 'SERIE-' . $numProd . $numLote . sprintf('%03d', $numSerie),
                        'estado' => ($numSerie === 1) ? 'vendida' : 'disponible', // Primera serie "vendida", resto "disponible"
                        'lote_id' => $lote->id,
                    ]);
                }
            }
        }
    }
}

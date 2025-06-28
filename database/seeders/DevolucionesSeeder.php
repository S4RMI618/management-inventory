<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Devolucion;
use App\Models\Producto;
use App\Models\Almacen;

class DevolucionesSeeder extends Seeder
{
    public function run(): void
    {
        $producto = Producto::skip(1)->first(); // segundo producto
        $almacen = Almacen::first();

        if (!$producto || !$almacen) {
            echo "No se encontró producto o almacén para la devolución.\n";
            return;
        }

        Devolucion::create([
            'producto_id' => $producto->id,
            'almacen_id' => $almacen->id,
            'cantidad' => 1,
            'motivo' => 'garantia',
            'detalle' => 'Cliente reportó mal funcionamiento del equipo dentro del período de garantía.',
        ]);
    }
}


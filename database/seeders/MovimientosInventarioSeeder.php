<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\SocioComercial;
use App\Models\User;

class MovimientosInventarioSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();
        $almacenes = Almacen::all();
        $socio = SocioComercial::first();
        $usuario = User::first(); // Asegúrate de tener al menos un usuario en users

        if ($productos->count() < 2 || $almacenes->count() < 2 || !$usuario || !$socio) {
            echo "Faltan datos para movimientos.\n";
            return;
        }

        $producto1 = $productos[0];
        $producto2 = $productos[1];
        $almacen1 = $almacenes[0];
        $almacen2 = $almacenes[1];

        // Entrada (compra de proveedor)
        MovimientoInventario::create([
            'producto_id' => $producto1->id,
            'almacen_origen_id' => null,
            'almacen_destino_id' => $almacen1->id,
            'socio_comercial_id' => $socio->id,
            'usuario_id' => $usuario->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
            'referencia_externa' => 'FACT-COMPRA-001',
            'fecha_documento' => now()->subDays(10),
            'observaciones' => 'Compra inicial de proveedor.',
        ]);

        // Salida (venta a cliente)
        MovimientoInventario::create([
            'producto_id' => $producto1->id,
            'almacen_origen_id' => $almacen1->id,
            'almacen_destino_id' => null,
            'socio_comercial_id' => $socio->id,
            'usuario_id' => $usuario->id,
            'tipo' => 'salida',
            'cantidad' => 1,
            'referencia_externa' => 'FACT-VENTA-001',
            'fecha_documento' => now()->subDays(5),
            'observaciones' => 'Venta de producto a cliente.',
        ]);

        // Traslado entre almacenes
        MovimientoInventario::create([
            'producto_id' => $producto2->id,
            'almacen_origen_id' => $almacen1->id,
            'almacen_destino_id' => $almacen2->id,
            'socio_comercial_id' => null,
            'usuario_id' => $usuario->id,
            'tipo' => 'traslado',
            'cantidad' => 2,
            'referencia_externa' => null,
            'fecha_documento' => now()->subDays(3),
            'observaciones' => 'Traslado interno entre sedes.',
        ]);

        // Devolución
        MovimientoInventario::create([
            'producto_id' => $producto2->id,
            'almacen_origen_id' => null,
            'almacen_destino_id' => $almacen1->id,
            'socio_comercial_id' => $socio->id,
            'usuario_id' => $usuario->id,
            'tipo' => 'devolucion',
            'cantidad' => 1,
            'referencia_externa' => 'DEV-001',
            'fecha_documento' => now()->subDays(2),
            'observaciones' => 'Devolución por falla técnica.',
        ]);
    }
}

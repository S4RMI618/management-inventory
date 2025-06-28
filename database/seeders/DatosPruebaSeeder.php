<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Serie;

class DatosPruebaSeeder extends Seeder
{
    public function run()
    {
        $almacen = Almacen::create([
            'nombre' => 'Almacén Central',
            'ubicacion' => 'Calle Principal #123',
        ]);

        $categoria = Categoria::create(['nombre' => 'Electrónica']);
        $marca = Marca::create(['nombre' => 'TechCorp']);

        $producto = Producto::create([
            'codigo' => 'PROD123',
            'nombre' => 'Router Inalámbrico',
            'modelo' => 'RTX1000',
            'marca_id' => $marca->id,
            'categoria_id' => $categoria->id,
            'precio_costo' => 50.00,
            'precio_venta' => 80.00,
            'ubicacion' => 'Estante A3',
            'estado' => 'activo',
            'tiene_invima' => false
        ]);

        $lote = Lote::create([
            'producto_id' => $producto->id,
            'numero_lote' => 'LOTE456',
            'fecha_fabricacion' => now()->subMonths(2),
            'fecha_vencimiento' => now()->addMonths(10),
            'tiene_invima' => false
        ]);

        Serie::create([
            'producto_id' => $producto->id,
            'almacen_id' => $almacen->id,
            'numero_serie' => 'SERIE789',
            'estado' => 'disponible',
        ]);

        Serie::create([
            'producto_id' => $producto->id,
            'almacen_id' => $almacen->id,
            'numero_serie' => 'SERIE790',
            'estado' => 'vendida',
        ]);
    }
}

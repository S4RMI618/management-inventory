<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Categoria;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        $marca1 = Marca::where('nombre', 'Samsung')->first();
        $marca2 = Marca::where('nombre', 'LG')->first();
        $marca3 = Marca::where('nombre', 'Sony')->first();
        $marca4 = Marca::where('nombre', 'Xiaomi')->first();

        $cat1 = Categoria::where('nombre', 'Electrodomésticos')->first();
        $cat2 = Categoria::where('nombre', 'Tecnología')->first();
        $cat3 = Categoria::where('nombre', 'Comida')->first();

        Producto::insert([
            [
                'codigo' => 'PROD001',
                'nombre' => 'Televisor LED 50"',
                'modelo' => 'TV50X123',
                'marca_id' => $marca1->id,
                'categoria_id' => $cat1->id,
                'precio_costo' => 1200000,
                'precio_venta' => 1500000,
                'ubicacion' => 'Estante 1',
                'estado' => 'activo',
                'tiene_invima' => false,
            ],
            [
                'codigo' => 'PROD002',
                'nombre' => 'Refrigerador Doble Puerta',
                'modelo' => 'FRG456',
                'marca_id' => $marca2->id,
                'categoria_id' => $cat1->id,
                'precio_costo' => 2000000,
                'precio_venta' => 2500000,
                'ubicacion' => 'Bodega',
                'estado' => 'activo',
                'tiene_invima' => false,
            ],
            [
                'codigo' => 'PROD003',
                'nombre' => 'Laptop Core i7 16GB',
                'modelo' => 'LTP789',
                'marca_id' => $marca3->id,
                'categoria_id' => $cat2->id,
                'precio_costo' => 3000000,
                'precio_venta' => 3600000,
                'ubicacion' => 'Oficina',
                'estado' => 'activo',
                'tiene_invima' => false,
            ],
            [
                'codigo' => 'PROD004',
                'nombre' => 'Termómetro Digital Clínico',
                'modelo' => null,
                'marca_id' => $marca4->id,
                'categoria_id' => $cat3->id,
                'precio_costo' => 5000,
                'precio_venta' => 9000,
                'ubicacion' => 'Góndola 3',
                'estado' => 'activo',
                'tiene_invima' => true,
            ],
            [
                'codigo' => 'PROD005',
                'nombre' => 'Purificador de aire doméstico',
                'modelo' => 'PUREAIR-X',
                'marca_id' => $marca1->id,
                'categoria_id' => $cat3->id,
                'precio_costo' => 450000,
                'precio_venta' => 600000,
                'ubicacion' => 'Estante alto',
                'estado' => 'activo',
                'tiene_invima' => true,
            ],
        ]);
    }
}


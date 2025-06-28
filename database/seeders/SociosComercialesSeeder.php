<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocioComercial;

class SociosComercialesSeeder extends Seeder
{
    public function run(): void
    {
        SocioComercial::insert([
            [
                'nombre' => 'Distribuidora ABC',
                'tipo_cliente' => true,
                'tipo_proveedor' => false,
                'documento' => '900123456',
                'direccion' => 'Cra 10 #20-30',
                'telefono' => '3100001111',
            ],
            [
                'nombre' => 'Proveedor XYZ',
                'tipo_cliente' => false,
                'tipo_proveedor' => true,
                'documento' => '800987654',
                'direccion' => 'Calle 45 #12-34',
                'telefono' => '3200002222',
            ],
            [
                'nombre' => 'Consumidor Final',
                'tipo_cliente' => true,
                'tipo_proveedor' => false,
                'documento' => '123456789',
                'direccion' => 'Av. Siempre Viva 742',
                'telefono' => '3000003333',
            ],
        ]);
    }
}


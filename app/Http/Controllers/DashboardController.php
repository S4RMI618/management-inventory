<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovimientoInventario;
use App\Models\SocioComercial;

class DashboardController extends Controller
{
    public function index()
    {
        // Movimientos recientes (últimos 5)
        $movimientos = MovimientoInventario::with(['producto', 'socioComercial', 'usuario'])
            ->latest()
            ->limit(5)
            ->get();

        // Total de clientes
        $totalClientes = SocioComercial::where('tipo_cliente', 1)->count();

        return view('dashboard', [
            'movimientos' => $movimientos,
            'totalClientes' => $totalClientes,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Serie;

class ProductSearchController extends Controller
{
    public function buscar(Request $request)
    {
        $codigo = $request->input('codigo');

        // Buscar por código de producto
        $producto = Producto::with('lotes', 'lotes.series')->where('codigo', $codigo)->first();

        if ($producto) {
            return view('dashboard', [
                'resultado' => $producto,
                'tipo' => 'producto'
            ]);
        }

        // Buscar por número de lote
        $lote = Lote::with('producto', 'series')->where('numero_lote', $codigo)->first();

        if ($lote) {
            return view('dashboard', [
                'resultado' => $lote,
                'tipo' => 'lote'
            ]);
        }

        // Buscar por número de serie
        $serie = Serie::with('producto')->where('numero_serie', $codigo)->first();

        if ($serie) {
            return view('dashboard', [
                'resultado' => $serie,
                'tipo' => 'serie'
            ]);
        }

        // Si no se encuentra ninguna coincidencia
        return view('dashboard', [
            'resultado' => null,
            'tipo' => 'ninguna'
        ]);
    }
}

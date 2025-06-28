<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Producto;
use App\Models\Serie;
use Illuminate\Http\Request;

class FiltrarProductoController extends Controller
{
    public function buscar(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);
        $codigo = $request->codigo;

        $productos = Producto::where('nombre', 'like', "%{$codigo}%")->get(); // Buscar por nombre del producto

        if ($productos->isEmpty()) {
            // Si no se encuentran productos, intentar buscar por lote
            $lote = Lote::with('producto')->where('numero_lote', $codigo)->first();
            if ($lote) {
                return response()->json([
                    'productos' => [
                        [
                            'id' => $lote->producto->id,
                            'nombre' => $lote->producto->nombre,
                            'codigo' => $lote->producto->codigo
                        ]
                    ]
                ]);
            }

            // Intentar buscar por serie
            $serie = Serie::with('producto')->where('numero_serie', $codigo)->where('estado', '!=', 'vendida')->first();
            if ($serie) {
                return response()->json([
                    'productos' => [
                        [
                            'id' => $serie->producto->id,
                            'nombre' => $serie->producto->nombre,
                            'codigo' => $serie->producto->codigo
                        ]
                    ]
                ]);
            }

            return response()->json(['error' => 'No se encontró ningún producto.'], 404);
        }

        return response()->json([
            'productos' => $productos->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo
                ];
            })
        ]);
    }
}

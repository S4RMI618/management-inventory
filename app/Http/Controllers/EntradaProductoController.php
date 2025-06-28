<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Lote;
use App\Models\Serie;
use App\Models\Almacen;
use Illuminate\Support\Facades\Auth;

class EntradaProductoController extends Controller
{
    public function index(){
        $productos = Producto::where('estado', 'activo')->get();
        return view('entradas.index', compact('productos'));
    }
    public function create()
    {
        // Obtener productos activos
        $productos = Producto::where('estado', 'activo')->get();

        // Obtener almacenes que manejan al menos un inventario del producto
        $almacenes = Almacen::whereHas('inventarios', function ($query) {
            $query->where('producto_id', '!=', null);
        })->get();

        return view('entradas.create', compact('productos', 'almacenes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'cantidad' => 'required|integer|min:1',
            'lote' => 'nullable|string',
            'serie.*' => 'nullable|string',
        ]);

        $productoId = $data['producto_id'];
        $almacenId = $data['almacen_id'];
        $cantidad = $data['cantidad'];

        // Registro en inventarios (update or insert)
        $inventario = Inventario::firstOrNew([
            'producto_id' => $productoId,
            'almacen_id' => $almacenId
        ]);
        $inventario->cantidad += $cantidad;
        $inventario->save();

        // Registro en movimientos
        MovimientoInventario::create([
            'producto_id' => $productoId,
            'almacen_destino_id' => $almacenId,
            'usuario_id' => Auth::id(),
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'fecha_documento' => now(),
            'observaciones' => 'Entrada manual desde sistema'
        ]);

        // Registrar lote si aplica
        if (!empty($data['lote'])) {
            Lote::create([
                'producto_id' => $productoId,
                'numero_lote' => $data['lote']
            ]);
        }

        // Registrar series si aplica
        if (!empty($data['serie'])) {
            foreach ($data['serie'] as $numSerie) {
                Serie::create([
                    'producto_id' => $productoId,
                    'almacen_id' => $almacenId,
                    'numero_serie' => $numSerie,
                    'estado' => 'disponible'
                ]);
            }
        }

        return redirect()->route('entrada.create')->with('success', 'Entrada registrada correctamente.');
    }
}

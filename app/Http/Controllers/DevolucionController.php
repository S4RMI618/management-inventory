<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Serie;
use App\Models\Producto;
use App\Models\Devolucion;
use App\Models\Inventario;
use App\Models\SocioComercial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function create()
    {
        return view('devoluciones.create');
    }

    /**
     * 🔍 Busca una serie y retorna sus datos + última venta registrada
     */
    public function buscarSerie($numero)
    {
        $serie = Serie::where('numero_serie', $numero)->first();

        if (!$serie) {
            return response()->json(['success' => false]);
        }

        // Buscar el último movimiento de salida (venta) asociado a ese producto
        $movimiento = MovimientoInventario::where('producto_id', $serie->producto_id)
            ->where('tipo', 'salida')
            ->whereNotNull('referencia_externa')
            ->latest()
            ->first();

        $cliente = $movimiento && $movimiento->socio_comercial_id
            ? SocioComercial::find($movimiento->socio_comercial_id)->nombre
            : 'N/A';

        $producto = Producto::with('marca', 'categoria')->find($serie->producto_id);

        return response()->json([
            'success' => true,
            'serie' => $serie,
            'producto' => $producto,
            'venta' => [
                'fecha' => optional($movimiento)->fecha_documento,
                'cliente' => $cliente,
                'factura' => optional($movimiento)->referencia_externa,
            ]
        ]);
    }

    /**
     * 📦 Registra la devolución de un producto seriado
     */
    public function store(Request $request)
    {
        $request->validate([
            'serie_id' => 'required|exists:series,id',
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'motivo' => 'required|string',
            'detalle' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Registrar devolución
            $devolucion = Devolucion::create([
                'producto_id' => $request->producto_id,
                'almacen_id' => $request->almacen_id,
                'cantidad' => 1, // siempre 1 si es seriado
                'motivo' => $request->motivo,
                'detalle' => $request->detalle,
            ]);

            // Crear movimiento de inventario
            MovimientoInventario::create([
                'producto_id' => $request->producto_id,
                'almacen_destino_id' => $request->almacen_id,
                'usuario_id' => Auth::id(),
                'tipo' => 'devolucion',
                'cantidad' => 1,
                'fecha_documento' => now(),
                'observaciones' => $request->detalle,
            ]);

            // Actualizar inventario (incrementar en 1)
            Inventario::updateOrCreate(
                ['producto_id' => $request->producto_id, 'almacen_id' => $request->almacen_id],
                ['cantidad' => DB::raw('cantidad + 1')]
            );

            // Actualizar estado de la serie → disponible (o averiada según lógica)
            Serie::where('id', $request->serie_id)->update([
                'estado' => $request->motivo === 'garantia' ? 'disponible' : 'averiada',
                'almacen_id' => $request->almacen_id
            ]);

            DB::commit();

            return redirect()->route('devoluciones.create')
                ->with('success', 'Devolución registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al registrar devolución: ' . $e->getMessage());
        }
    }
}

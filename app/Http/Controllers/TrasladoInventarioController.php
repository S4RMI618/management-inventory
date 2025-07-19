<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Serie;
use App\Models\MovimientoInventario;
use App\Models\Inventario;
use App\Models\Almacen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class TrasladoInventarioController extends Controller
{
    public function create()
    {
        $almacenes = Almacen::all();
        return view('traslados.create', compact('almacenes'));
    }

    // 1) Productos con stock >0 en un almacén
    public function getProductos(Almacen $almacen)
    {
        $productos = Inventario::with('producto')
            ->where('almacen_id', $almacen->id)
            ->where('cantidad', '>', 0)
            ->get()
            ->pluck('producto')
            ->unique('id')
            ->map(fn($p) => [
                'id'     => $p->id,
                'codigo' => $p->codigo,
                'nombre' => $p->nombre,
            ]);

        return response()->json($productos);
    }

    // 2) Lotes de un producto, pero sólo aquellos con series disponibles en el almacén
    public function getLotes(Producto $producto, Request $request)
    {
        $origen = $request->query('origen');
        $query  = $producto->lotes()->whereHas('series', fn($q) =>
            $q->where('estado','disponible')
              ->when($origen, fn($q2) => $q2->where('almacen_id',$origen))
        );
        return response()->json($query->get(['id','numero_lote']));
    }

    // 3) Series disponibles de un lote, opcionalmente filtradas por almacén
    public function getSeries(Lote $lote, Request $request)
    {
        $origen = $request->query('origen');
        $q = $lote->series()->where('estado','disponible')
               ->when($origen, fn($q2)=> $q2->where('almacen_id',$origen));
        return response()->json($q->get(['id','numero_serie']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'series_ids'           => 'required|array|min:1',
            'series_ids.*'         => 'exists:series,id',
            'almacen_origen_id'    => 'required|exists:almacenes,id',
            'almacen_destino_id'   => 'required|different:almacen_origen_id|exists:almacenes,id',
            'observaciones'        => 'nullable|string',
        ]);

        $origenId  = $data['almacen_origen_id'];
        $destinoId = $data['almacen_destino_id'];
        $obs       = $data['observaciones'] ?? null;

        foreach ($data['series_ids'] as $serieId) {
            DB::transaction(function() use ($serieId, $origenId, $destinoId, $obs) {
                $serie = Serie::findOrFail($serieId);
                // mover serie
                $serie->update(['almacen_id'=>$destinoId]);
                // registro salida
                MovimientoInventario::create([
                  'producto_id'       => $serie->producto_id,
                  'almacen_origen_id' => $origenId,
                  'tipo'              => 'traslado',
                  'cantidad'          => 1,
                  'usuario_id'        => Auth::id(),
                  'fecha_documento'   => now(),
                  'observaciones'     => $obs ? "Salida: $obs" : "Salida serie {$serie->numero_serie}",
                ]);
                // registro entrada
                MovimientoInventario::create([
                  'producto_id'        => $serie->producto_id,
                  'almacen_destino_id' => $destinoId,
                  'tipo'               => 'traslado',
                  'cantidad'           => 1,
                  'usuario_id'         => Auth::id(),
                  'fecha_documento'    => now(),
                  'observaciones'      => $obs ? "Entrada: $obs" : "Entrada serie {$serie->numero_serie}",
                ]);
                // ajustar inventario
                Inventario::where([
                  ['producto_id',$serie->producto_id],
                  ['almacen_id',$origenId]
                ])->decrement('cantidad',1);
                Inventario::updateOrCreate(
                  ['producto_id'=>$serie->producto_id,'almacen_id'=>$destinoId],
                  ['cantidad'=>DB::raw('cantidad+1')]
                );
            });
        }

        return back()->with('success','Traslado registrado correctamente.');
    }
}

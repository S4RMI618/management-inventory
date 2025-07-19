<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Serie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MovimientoInventario;
use App\Models\Inventario;
use App\Models\Almacen;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function create()
    {
        $almacenes = Almacen::all();
        $productos = Producto::with('marca')->get();

        return view('ventas.create', [
            'almacenes' => $almacenes,
            'productos' => $productos,
        ]);
    }
    public function buscarProducto(Request $request)
    {
        $input = $request->input('filtro');
        $almacenId = $request->input('almacen_id');

        // Buscar por código de producto
        $producto = Producto::where('codigo', $input)->first();
        if ($producto) {
            $lotes = $producto->lotes()->withCount(['series as disponibles' => function ($q) use ($almacenId) {
                $q->where('estado', 'disponible')->where('almacen_id', $almacenId);
            }])->get();

            $series = $producto->series()->where('estado', 'disponible')->where('almacen_id', $almacenId)->get();

            return response()->json([
                'tipo' => 'producto',
                'producto' => $producto,
                'lotes' => $lotes,
                'series' => $series,
            ]);
        }

        // Buscar por modelo
        $producto = Producto::where('modelo', $input)->first();
        if ($producto) {
            $lotes = $producto->lotes()->withCount(['series as disponibles' => function ($q) use ($almacenId) {
                $q->where('estado', 'disponible')->where('almacen_id', $almacenId);
            }])->get();

            $series = $producto->series()->where('estado', 'disponible')->where('almacen_id', $almacenId)->get();

            return response()->json([
                'tipo' => 'producto',
                'producto' => $producto,
                'lotes' => $lotes,
                'series' => $series,
            ]);
        }

        // Buscar por lote
        $lote = Lote::where('numero_lote', $input)->first();
        if ($lote) {
            $series = Serie::where('lote_id', $lote->id)
                ->where('estado', 'disponible')
                ->where('almacen_id', $almacenId)
                ->get();

            return response()->json([
                'tipo' => 'lote',
                'lote' => $lote,
                'series' => $series,
            ]);
        }

        // Buscar por serie
        $serie = Serie::where('numero_serie', $input)
            ->where('estado', 'disponible')
            ->where('almacen_id', $almacenId)
            ->first();

        if ($serie) {
            return response()->json([
                'tipo' => 'serie',
                'serie' => $serie,
            ]);
        }

        // No se encontró nada
        return response()->json([
            'tipo' => 'none',
            'message' => 'No se encontró coincidencia.'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'almacen_id'    => 'required|exists:almacenes,id',
            'detalle_venta' => 'required',
        ]);

        $almacen_id = $request->input('almacen_id');
        $detalle = json_decode($request->input('detalle_venta'), true);

        if (!is_array($detalle) || empty($detalle)) {
            return back()->with('error', 'No se recibieron productos para la venta.');
        }

        DB::beginTransaction();

        try {
            $movimientos = [];

            foreach ($detalle as $item) {
                // ---- 1. Para lotes completos ----
                if ($item['tipo'] === 'lote') {
                    $seriesNecesarias = $item['cantidad'];

                    // Series DISPONIBLES en el almacén actual
                    $series = Serie::where('lote_id', $item['lote_id'])
                        ->where('almacen_id', $almacen_id)
                        ->where('estado', 'disponible')
                        ->limit($seriesNecesarias)
                        ->get();

                    $faltantes = $seriesNecesarias - $series->count();

                    // Si FALTAN series, buscamos en otros almacenes
                    if ($faltantes > 0) {
                        $otrasSeries = Serie::where('lote_id', $item['lote_id'])
                            ->where('almacen_id', '!=', $almacen_id)
                            ->where('estado', 'disponible')
                            ->limit($faltantes)
                            ->get();

                        foreach ($otrasSeries as $serie) {
                            // Traslado automático: crea movimiento de traslado
                            MovimientoInventario::create([
                                'producto_id'         => $serie->producto_id,
                                'almacen_origen_id'   => $serie->almacen_id,
                                'almacen_destino_id'  => $almacen_id,
                                'socio_comercial_id'  => null,
                                'usuario_id'          => Auth::id(),
                                'tipo'                => 'traslado',
                                'cantidad'            => 1,
                                'referencia_externa'  => null,
                                'fecha_documento'     => now(),
                                'observaciones'       => 'Traslado automático previo a venta (lote)',
                            ]);
                            // Actualiza el almacén de la serie
                            Inventario::where('almacen_id', $serie->almacen_id)
                                ->where('producto_id', $serie->producto_id)
                                ->decrement('cantidad', 1);
                            Inventario::where('almacen_id', $almacen_id)
                                ->where('producto_id', $serie->producto_id)
                                ->increment('cantidad', 1);
                            $serie->almacen_id = $almacen_id;
                            $serie->save();

                            $series->push($serie);
                        }
                    }

                    // Ahora todas las series requeridas están en el almacén de venta
                    foreach ($series as $serie) {
                        $serie->estado = 'vendida';
                        $serie->save();

                        $movimientos[] = [
                            'producto_id'         => $serie->producto_id,
                            'almacen_origen_id'   => $almacen_id,
                            'almacen_destino_id'  => null,
                            'socio_comercial_id'  => null,
                            'usuario_id'          => Auth::id(),
                            'tipo'                => 'salida',
                            'cantidad'            => 1,
                            'referencia_externa'  => null,
                            'fecha_documento'     => now(),
                            'observaciones'       => 'Venta por lote. Serie: ' . $serie->numero_serie,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];
                    }

                    // Descontar inventario por cantidad en el almacén de venta
                    Inventario::where('almacen_id', $almacen_id)
                        ->where('producto_id', $item['producto_id'])
                        ->decrement('cantidad', $item['cantidad']);
                }

                // ---- 2. Para series individuales ----
                elseif ($item['tipo'] === 'serie') {
                    $serie = Serie::find($item['serie_id']);
                    if ($serie && $serie->estado === 'disponible') {
                        // Si la serie está en otro almacén, traslado automático
                        if ($serie->almacen_id != $almacen_id) {
                            MovimientoInventario::create([
                                'producto_id'         => $serie->producto_id,
                                'almacen_origen_id'   => $serie->almacen_id,
                                'almacen_destino_id'  => $almacen_id,
                                'socio_comercial_id'  => null,
                                'usuario_id'          => Auth::id(),
                                'tipo'                => 'traslado',
                                'cantidad'            => 1,
                                'referencia_externa'  => null,
                                'fecha_documento'     => now(),
                                'observaciones'       => 'Traslado automático previo a venta (serie)',
                            ]);
                            // Actualiza inventarios y serie
                            Inventario::where('almacen_id', $serie->almacen_id)
                                ->where('producto_id', $serie->producto_id)
                                ->decrement('cantidad', 1);
                            Inventario::where('almacen_id', $almacen_id)
                                ->where('producto_id', $serie->producto_id)
                                ->increment('cantidad', 1);

                            $serie->almacen_id = $almacen_id;
                            $serie->save();
                        }

                        // Marca como vendida
                        $serie->estado = 'vendida';
                        $serie->save();

                        $movimientos[] = [
                            'producto_id'         => $serie->producto_id,
                            'almacen_origen_id'   => $almacen_id,
                            'almacen_destino_id'  => null,
                            'socio_comercial_id'  => null,
                            'usuario_id'          => Auth::id(),
                            'tipo'                => 'salida',
                            'cantidad'            => 1,
                            'referencia_externa'  => null,
                            'fecha_documento'     => now(),
                            'observaciones'       => 'Venta por serie: ' . $serie->numero_serie,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];

                        // Descontar inventario
                        Inventario::where('almacen_id', $almacen_id)
                            ->where('producto_id', $serie->producto_id)
                            ->decrement('cantidad', 1);
                    }
                }
            }

            // Inserta todos los movimientos de salida (la venta) de una vez
            MovimientoInventario::insert($movimientos);

            DB::commit();
            return redirect()->route('ventas.create')->with('success', 'Venta registrada, stock actualizado y traslados automáticos realizados si fue necesario.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar la venta: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar la venta: ' . $e->getMessage());
        }
    }
}

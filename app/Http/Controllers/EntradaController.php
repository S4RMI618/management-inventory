<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Lote;
use App\Models\Serie;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EntradaController extends Controller
{
    public function create()
    {
        $productos = Producto::with('marca')
            ->select('id', 'nombre', 'modelo', 'codigo', 'marca_id')
            ->get();

        return view('entradas.create', [
            'productos' => $productos,
            'almacenes' => Almacen::all(),
        ]);
    }

    public function store(Request $request)
    {
        /* MENSAJES PERSONALIZADOS */
        $mensajes = [
            'producto_id.required' => 'Por favor, selecciona un producto.',
            'producto_id.exists'   => 'El producto seleccionado no es válido.',
            'almacen_id.required'  => 'Por favor, selecciona un almacén.',
            'almacen_id.exists'    => 'El almacén seleccionado no es válido.',
            'cantidad.required'    => 'Por favor, ingresa la cantidad.',
            'cantidad.numeric'     => 'La cantidad debe ser un número.',
            'cantidad.min'         => 'La cantidad debe ser al menos 1.',
            'series.required'      => 'Por favor, ingresa al menos una serie.',
            'numero_lote.required' => 'Por favor, ingresa el número de lote.',
            'fecha_fabricacion.required'  => 'La fecha de fabricación no es válida, intenta de nuevo.',
            'fecha_vencimiento.date'  => 'La fecha de vencimiento no es una fecha válida.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fabricación.',
            'tiene_invima.required' => 'Por favor, indica si el producto tiene INVIMA.',
        ];
        $series = preg_split('/[\s\n]+/', $request->input('series'), -1, PREG_SPLIT_NO_EMPTY);
        if (count($series) === 0) {
            return back()
                ->withInput()
                ->withErrors(['series' => 'Debes ingresar al menos una serie válida.']);
        }

        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id'  => 'required|exists:almacenes,id',
            'series'      => 'required|string',
            'numero_lote' => 'required|string|max:255',
            'fecha_fabricacion'  => 'required|date',
            'tiene_vencimiento'  => 'nullable',
            'fecha_vencimiento'  => 'nullable|date|after_or_equal:fecha_fabricacion',
            'tiene_invima'       => 'required|boolean',
        ], $mensajes);

        try {
            DB::transaction(function () use ($request) {
                $producto = Producto::findOrFail($request->producto_id);
                $almacen  = Almacen::findOrFail($request->almacen_id);

                if ($request->filled('numero_lote')) {
                    $lote = Lote::firstOrNew([
                        'producto_id' => $producto->id,
                        'numero_lote' => $request->numero_lote,
                    ]);
                    $lote->fecha_fabricacion = $request->fecha_fabricacion;
                    // Sólo asigna vencimiento si se marcó el checkbox
                    $lote->fecha_vencimiento = $request->has('tiene_vencimiento')
                        ? $request->fecha_vencimiento
                        : null;
                    // Checkbox INVIMA → 1 o 0
                    $lote->tiene_invima = $request->boolean('tiene_invima');
                    $lote->save();
                }
                // Obtener el ID del lote recién creado o actualizado
                $lote_id = $lote->id;

                $series = preg_split('/[\s\n]+/', $request->input('series'), -1, PREG_SPLIT_NO_EMPTY);
                foreach ($series as $serie) {
                    Serie::create([
                        'producto_id'   => $producto->id,
                        'almacen_id'    => $almacen->id,
                        'lote_id'       => $lote_id,    
                        'numero_serie'  => $serie,
                        'estado'        => 'disponible',
                    ]);
                }

                $cantidad = count($series);

                MovimientoInventario::create([
                    'producto_id'         => $producto->id,
                    'almacen_destino_id'  => $almacen->id,
                    'usuario_id'          => optional(Auth::user())->id,
                    'tipo'                => 'entrada',
                    'cantidad'            => $cantidad,
                    'fecha_documento'     => now(),
                ]);

                $inventario = Inventario::firstOrNew([
                    'producto_id' => $producto->id,
                    'almacen_id'  => $almacen->id,
                ]);

                $inventario->cantidad = ($inventario->exists
                    ? $inventario->cantidad + $cantidad
                    : $cantidad);
                $inventario->save();
            });

            return redirect()
                ->route('entradas.create')
                ->with('success', 'Entrada registrada correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al registrar entrada: " . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar la entrada. Intenta de nuevo.');
        }
    }
}

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
use Illuminate\Validation\Validator;


class EntradaController extends Controller
{
    public function create()
    {
        return view('entradas.create', [
            'almacenes' => Almacen::all(),
        ]);
    }

    // NUEVO: búsqueda server-side
    public function buscarProductos(Request $request)
    {
        $validated = $request->validate([
            'q'        => ['nullable', 'string', 'max:100'],
            'page'     => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $q       = $validated['q'] ?? '';
        $perPage = $validated['per_page'] ?? 10;

        $query = Producto::query()
            ->with(['marca:id,nombre'])
            ->select('id', 'nombre', 'modelo', 'codigo', 'marca_id', 'tiene_series') // <- AÑADIDO
            ->when($q !== '', function ($qb) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
                $qb->where(function ($sub) use ($like) {
                    $sub->where('nombre', 'like', $like)
                        ->orWhere('modelo', 'like', $like)
                        ->orWhere('codigo', 'like', $like);
                });
            })
            ->orderBy('nombre');

        $results = $query->paginate($perPage)->appends(['q' => $q]);

        return response()->json([
            'data' => $results->map(fn($p) => [
                'id'           => $p->id,
                'nombre'       => $p->nombre,
                'modelo'       => $p->modelo,
                'codigo'       => $p->codigo,
                'tiene_series' => $p->tiene_series, // <- YA VIENE DEL SELECT
                'marca'        => $p->marca ? ['id' => $p->marca->id, 'nombre' => $p->marca->nombre] : null,
            ]),
            'meta' => [
                'total'        => $results->total(),
                'per_page'     => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page'    => $results->lastPage(),
                'has_more'     => $results->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        /* MENSAJES PERSONALIZADOS (top-level) */
        $mensajes = [
            'almacen_id.required'  => 'Por favor, selecciona un almacén.',
            'almacen_id.exists'    => 'El almacén seleccionado no es válido.',
            'detalle_entrada.required' => 'No hay líneas de entrada para procesar.',
        ];

        // Validación top-level (no exigimos numero_lote/series globales)
        $request->validate([
            'almacen_id'      => 'required|exists:almacenes,id',
            'detalle_entrada' => 'required|string', // JSON de líneas
        ], $mensajes);

        // Decodificar líneas
        $items = json_decode($request->input('detalle_entrada'), true);
        if (!is_array($items) || count($items) === 0) {
            return back()->withInput()->withErrors([
                'detalle_entrada' => 'No hay líneas de entrada.'
            ]);
        }

        try {
            DB::transaction(function () use ($request, $items) {
                $almacen = Almacen::findOrFail($request->almacen_id);

                foreach ($items as $idx => $it) {
                    // -------- Validación por línea --------
                    $tieneSeries = !empty($it['tiene_series']);

                    $rules = [
                        'producto_id'       => 'required|exists:productos,id',
                        'fecha_fabricacion' => 'required|date',
                        'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_fabricacion',
                        // cantidad se valida abajo según tenga/no tenga series
                    ];

                    if ($tieneSeries) {
                        $rules['numero_lote'] = 'required|string|max:50';
                        $rules['series']      = 'required|array|min:1';
                        $rules['series.*']    = 'string|max:255';
                    } else {
                        $rules['cantidad']    = 'required|integer|min:1';
                        // lote/series NO requeridos
                    }

                    $mensajesLinea = [
                        'producto_id.required'       => "Línea " . ($idx + 1) . ": selecciona un producto.",
                        'producto_id.exists'         => "Línea " . ($idx + 1) . ": el producto no es válido.",
                        'numero_lote.required'       => "Línea " . ($idx + 1) . ": el lote es obligatorio para productos con series.",
                        'series.required'            => "Línea " . ($idx + 1) . ": debes ingresar al menos una serie.",
                        'series.array'               => "Línea " . ($idx + 1) . ": formato de series inválido.",
                        'fecha_fabricacion.required' => "Línea " . ($idx + 1) . ": ingresa la fecha de fabricación.",
                        'fecha_vencimiento.after_or_equal' => "Línea " . ($idx + 1) . ": la fecha de vencimiento debe ser igual o posterior a la fabricación.",
                        'cantidad.required'          => "Línea " . ($idx + 1) . ": ingresa la cantidad.",
                        'cantidad.integer'           => "Línea " . ($idx + 1) . ": la cantidad debe ser un número.",
                        'cantidad.min'               => "Línea " . ($idx + 1) . ": la cantidad debe ser al menos 1.",
                    ];

                    $validator = Validator($it, $rules, $mensajesLinea);
                    if ($validator->fails()) {
                        throw new \Illuminate\Validation\ValidationException($validator);
                    }

                    // -------- Persistencia por línea --------
                    $producto = Producto::findOrFail($it['producto_id']);

                    // Vencimiento (puede venir null)
                    $fechaVenc = $it['fecha_vencimiento'] ?? null;

                    // INVIMA (de la línea o del form; por defecto 0)
                    $tieneInvimaLinea = isset($it['tiene_invima']) ? (bool)$it['tiene_invima'] : (bool)$request->boolean('tiene_invima');

                    $lote_id = null;
                    $cantidad = 0;

                    if ($tieneSeries) {
                        // Crear/obtener Lote
                        $lote = Lote::firstOrNew([
                            'producto_id' => $producto->id,
                            'numero_lote' => $it['numero_lote'],
                        ]);
                        $lote->fecha_fabricacion = $it['fecha_fabricacion'];
                        $lote->fecha_vencimiento = $fechaVenc;
                        $lote->tiene_invima      = $tieneInvimaLinea ? 1 : 0;
                        $lote->save();

                        $lote_id = $lote->id;

                        // Crear Series
                        $seriesArray = array_map('trim', $it['series'] ?? []);
                        $seriesArray = array_filter($seriesArray, fn($s) => $s !== '');

                        // (Opcional) Validación de duplicados en BD:
                        // foreach ($seriesArray as $snum) {
                        //     if (Serie::where('numero_serie', $snum)->exists()) {
                        //         throw \Illuminate\Validation\ValidationException::withMessages([
                        //             'series' => ["Línea ".($idx+1).": la serie '{$snum}' ya existe."]
                        //         ]);
                        //     }
                        // }

                        foreach ($seriesArray as $snum) {
                            Serie::create([
                                'producto_id'  => $producto->id,
                                'almacen_id'   => $almacen->id,
                                'lote_id'      => $lote_id,
                                'numero_serie' => $snum,
                                'estado'       => 'disponible',
                            ]);
                        }

                        $cantidad = count($seriesArray);
                    } else {
                        // Sin series: cantidad manual
                        $cantidad = (int)($it['cantidad'] ?? 0);
                        if ($cantidad < 1) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'cantidad' => ["Línea " . ($idx + 1) . ": la cantidad debe ser al menos 1."]
                            ]);
                        }
                        // No crear lote ni series
                    }

                    // Movimiento de inventario por línea
                    MovimientoInventario::create([
                        'producto_id'         => $producto->id,
                        'almacen_destino_id'  => $almacen->id,
                        'usuario_id'          => optional(Auth::user())->id,
                        'tipo'                => 'entrada',
                        'cantidad'            => $cantidad,
                        'fecha_documento'     => now(),
                    ]);

                    // Actualizar/crear Inventario
                    $inventario = Inventario::firstOrNew([
                        'producto_id' => $producto->id,
                        'almacen_id'  => $almacen->id,
                        'costo' => $producto->precio_costo,
                    ]);
                    $inventario->cantidad = ($inventario->exists ? $inventario->cantidad : 0) + $cantidad;
                    $inventario->save();
                }
            });
            return redirect()
                ->route('entradas.create')
                ->with('success', 'Entrada(s) registrada(s) correctamente.');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Devuelve errores de validación por línea
            return back()->withInput()->withErrors($ve->errors());
        } catch (\Exception $e) {
            Log::error("Error al registrar entrada: " . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar la entrada. Intenta de nuevo.');
        }
    }
};

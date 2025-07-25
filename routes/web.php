<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FiltrarProductoController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\TrasladoInventarioController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    /* DASHBOARD */
    Route::post('/buscar-producto', [ProductSearchController::class, 'buscar'])->name('buscar.producto');
    Route::post('/buscar-datos-producto', [FiltrarProductoController::class, 'buscar'])->name('producto.buscar.ajax');

    // Ruta para el formulario de la venta
    Route::get('/venta/crear', [VentaController::class, 'create'])->name('ventas.create');
    // Ruta para registrar la venta
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    // Ruta para buscar productos en la venta
    Route::get('/venta/buscar', [VentaController::class, 'buscarProducto'])->name('ventas.buscar');
    // Ruta para buscar clientes en la venta
    Route::get('/clientes/buscar', [VentaController::class, 'buscarCliente'])->name('clientes.buscar');





    // RUTAS TRASLADO DE INVENTARIO
    Route::get('traslados/create',           [TrasladoInventarioController::class, 'create'])->name('traslados.create');
    Route::post('traslados',                 [TrasladoInventarioController::class, 'store'])->name('traslados.store');
    Route::get('almacenes/{almacen}/productos', [TrasladoInventarioController::class, 'getProductos']);
    Route::get('productos/{producto}/lotes',    [TrasladoInventarioController::class, 'getLotes']);
    Route::get('lotes/{lote}/series',           [TrasladoInventarioController::class, 'getSeries']);


    // RUTAS PARA DEVOLUCIONES
    Route::get('/devoluciones/create', [DevolucionController::class, 'create'])->name('devoluciones.create');
    Route::post('/devoluciones', [DevolucionController::class, 'store'])->name('devoluciones.store');
    Route::get('/ventas/{referencia}/productos', [DevolucionController::class, 'productosPorVenta']);
    Route::get('/buscar-serie/{numero}', [DevolucionController::class, 'buscarSerie']);
});

/* ENTRADAS ROUTES */
Route::middleware(['auth'])->group(function () {
    Route::get('/entradas/create', [EntradaController::class, 'create'])->name('entradas.create');
    Route::post('/entradas', [EntradaController::class, 'store'])->name('entradas.store');
});



require __DIR__ . '/auth.php';

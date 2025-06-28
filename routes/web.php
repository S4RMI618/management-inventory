<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntradaProductoController;
use App\Http\Controllers\FiltrarProductoController;
use App\Http\Controllers\ProductSearchController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/entrada-producto', [EntradaProductoController::class, 'create'])->name('entrada.create');
    Route::post('/entrada-producto', [EntradaProductoController::class, 'store'])->name('entrada.store');
});

/* DASHBOARD */
Route::post('/buscar-producto', [ProductSearchController::class, 'buscar'])->name('buscar.producto');

Route::post('/buscar-datos-producto', [FiltrarProductoController::class, 'buscar'])->name('producto.buscar.ajax');


require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntradaProductoController;

Route::post('/entradas', [EntradaProductoController::class, 'store']);

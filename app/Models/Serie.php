<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Serie extends Model
{
    use HasFactory;
    public const ESTADOS = ['disponible', 'vendida'];
    protected $fillable = ['producto_id', 'almacen_id', 'numero_serie', 'estado'];// ESTADO: EN BODEGA O VENDIDO

    public function producto() { return $this->belongsTo(Producto::class); }
    public function almacen() { return $this->belongsTo(Almacen::class); }
} 
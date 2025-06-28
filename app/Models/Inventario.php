<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model
{
    use HasFactory;
    protected $fillable = ['almacen_id', 'producto_id', 'cantidad'];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function almacen() { return $this->belongsTo(Almacen::class); }
}
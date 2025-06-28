<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Devolucion extends Model
{
    use HasFactory;
    protected $table = 'devoluciones';
    protected $fillable = ['producto_id', 'almacen_id', 'cantidad', 'motivo', 'detalle'];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function almacen() { return $this->belongsTo(Almacen::class); }
}

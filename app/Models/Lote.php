<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lote extends Model
{
    use HasFactory;
    protected $fillable = ['producto_id', 'numero_lote', 'fecha_fabricacion', 'fecha_vencimiento', 'estado', 'tiene_invima'];
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function series()
    {
        return $this->hasMany(Serie::class, 'producto_id', 'producto_id');
    }
}

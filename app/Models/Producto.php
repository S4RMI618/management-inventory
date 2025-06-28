<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;
    protected $fillable = ['codigo', 'nombre', 'modelo', 'marca_id', 'categoria_id', 'precio_costo', 'precio_venta', 'ubicacion', 'estado', 'tiene_invima'];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }
    public function series()
    {
        return $this->hasMany(Serie::class);
    }
    public function esSeriado()
    {
        return $this->series()->exists();
    }

    public function tieneLote()
    {
        return $this->lotes()->exists();
    }
}

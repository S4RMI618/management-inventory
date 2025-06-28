<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacenes';
    protected $fillable = ['nombre', 'ubicacion'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
    public function series()
    {
        return $this->hasMany(Serie::class);
    }
    public function movimientosOrigen()
    {
        return $this->hasMany(MovimientoInventario::class, 'almacen_origen_id');
    }

    public function movimientosDestino()
    {
        return $this->hasMany(MovimientoInventario::class, 'almacen_destino_id');
    }
}

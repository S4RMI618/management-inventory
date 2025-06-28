<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocioComercial extends Model
{
    use HasFactory;
    protected $table = 'socios_comerciales';
    protected $fillable = ['nombre', 'tipo_cliente', 'tipo_proveedor', 'documento', 'direccion', 'telefono'];

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
    public function getTipoAttribute()
    {
        if ($this->tipo_cliente && $this->tipo_proveedor) return 'Cliente / Proveedor';
        if ($this->tipo_cliente) return 'Cliente';
        if ($this->tipo_proveedor) return 'Proveedor';
        return 'CONSUMIDOR FINAL';
    }
}

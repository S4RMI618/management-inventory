<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoInventario extends Model
{
    use HasFactory;
    protected $table = 'movimientos_inventario';
    protected $fillable = [
        'producto_id',
        'almacen_origen_id',
        'almacen_destino_id',
        'socio_comercial_id',
        'usuario_id',
        'tipo',
        'cantidad',
        'referencia_externa',
        'fecha_documento',
        'observaciones'
    ];
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }
    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }
    public function socioComercial()
    {
        return $this->belongsTo(SocioComercial::class);
    }
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}

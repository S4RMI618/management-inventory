<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Almacen extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'ubicacion'];

    public function inventarios() { return $this->hasMany(Inventario::class); }
    public function series() { return $this->hasMany(Serie::class); }
}

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = ['nombre'];
    public function productos() { return $this->hasMany(Producto::class); }
}

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nombre'];
    public function productos() { return $this->hasMany(Producto::class); }
}

class Producto extends Model
{
    use HasFactory;
    protected $fillable = ['codigo', 'nombre', 'modelo', 'marca_id', 'categoria_id', 'precio_costo', 'precio_venta', 'ubicacion', 'estado', 'tiene_invima'];

    public function marca() { return $this->belongsTo(Marca::class); }
    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function inventarios() { return $this->hasMany(Inventario::class); }
    public function lotes() { return $this->hasMany(Lote::class); }
    public function series() { return $this->hasMany(Serie::class); }
}

class Inventario extends Model
{
    use HasFactory;
    protected $fillable = ['almacen_id', 'producto_id', 'cantidad'];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function almacen() { return $this->belongsTo(Almacen::class); }
}

class Lote extends Model
{
    use HasFactory;
    protected $fillable = ['producto_id', 'numero_lote', 'fecha_fabricacion', 'fecha_vencimiento', 'tiene_invima'];
    public function producto() { return $this->belongsTo(Producto::class); }
}

class Serie extends Model
{
    use HasFactory;
    protected $fillable = ['producto_id', 'almacen_id', 'numero_serie', 'estado'];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function almacen() { return $this->belongsTo(Almacen::class); }
}

class SocioComercial extends Model
{
    use HasFactory;
    protected $table = 'socios_comerciales';
    protected $fillable = ['nombre', 'tipo_cliente', 'tipo_proveedor', 'documento', 'direccion', 'telefono'];

    public function movimientos() { return $this->hasMany(MovimientoInventario::class); }
}

class MovimientoInventario extends Model
{
    use HasFactory;
    protected $fillable = ['producto_id', 'almacen_origen_id', 'almacen_destino_id', 'socio_comercial_id', 'usuario_id', 'tipo', 'cantidad', 'referencia_externa', 'fecha_documento', 'observaciones'];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function almacenOrigen() { return $this->belongsTo(Almacen::class, 'almacen_origen_id'); }
    public function almacenDestino() { return $this->belongsTo(Almacen::class, 'almacen_destino_id'); }
    public function socioComercial() { return $this->belongsTo(SocioComercial::class); }
    public function usuario() { return $this->belongsTo(User::class); }
}

class Devolucion extends Model
{
    use HasFactory;
    protected $fillable = ['producto_id', 'almacen_id', 'cantidad', 'motivo', 'detalle'];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function almacen() { return $this->belongsTo(Almacen::class); }
}

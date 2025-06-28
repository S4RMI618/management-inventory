<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Almacenes
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ubicacion')->nullable();
            $table->timestamps();
        });

        // Categorias
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        // Marcas
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        // Productos
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('modelo')->nullable();
            $table->foreignId('marca_id')->constrained();
            $table->foreignId('categoria_id')->constrained();
            $table->decimal('precio_costo', 15, 2);
            $table->decimal('precio_venta', 15, 2);
            $table->string('ubicacion')->nullable();
            $table->string('estado');
            $table->boolean('tiene_invima')->default(false);
            $table->timestamps();
        });

        // Inventarios
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_id')->constrained();
            $table->foreignId('producto_id')->constrained();
            $table->decimal('cantidad', 15, 4)->default(0);
            $table->timestamps();

            $table->unique(['almacen_id', 'producto_id']);
        });

        // Lotes
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained();
            $table->string('numero_lote')->index();
            $table->date('fecha_fabricacion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('tiene_invima')->default(false);
            $table->timestamps();
        });

        // Series
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained();
            $table->foreignId('almacen_id')->constrained();
            $table->string('numero_serie')->unique();
            $table->enum('estado', ['disponible', 'vendida', 'averiada', 'vencida', 'mal_estado'])->default('disponible');
            $table->timestamps();
        });

        // Socios Comerciales
        Schema::create('socios_comerciales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('tipo_cliente')->default(false);
            $table->boolean('tipo_proveedor')->default(false);
            $table->string('documento');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->timestamps();
        });

        // Movimientos Inventario
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained();
            $table->foreignId('almacen_origen_id')->nullable()->constrained('almacenes');
            $table->foreignId('almacen_destino_id')->nullable()->constrained('almacenes');
            $table->foreignId('socio_comercial_id')->nullable()->constrained('socios_comerciales');
            $table->foreignId('usuario_id')->constrained('users');
            $table->enum('tipo', ['entrada', 'salida', 'traslado', 'devolucion']);
            $table->decimal('cantidad', 15, 4);
            $table->string('referencia_externa')->nullable();
            $table->date('fecha_documento')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Devoluciones
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained();
            $table->foreignId('almacen_id')->constrained();
            $table->decimal('cantidad', 15, 4);
            $table->enum('motivo', ['garantia', 'perdida', 'otro']);
            $table->text('detalle')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('socios_comerciales');
        Schema::dropIfExists('series');
        Schema::dropIfExists('lotes');
        Schema::dropIfExists('inventarios');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('almacenes');
    }
};

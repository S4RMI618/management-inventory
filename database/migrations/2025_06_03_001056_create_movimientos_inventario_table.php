<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};

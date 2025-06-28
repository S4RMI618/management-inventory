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

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socios_comerciales');
    }
};

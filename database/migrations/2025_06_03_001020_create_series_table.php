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
        // Series
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained();
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->foreignId('lote_id')->nullable();
            $table->foreign('lote_id')
                ->references('id')
                ->on('lotes')
                ->onDelete('set null');
            $table->string('numero_serie')->unique();
            $table->enum('estado', ['disponible', 'vendida', 'averiada', 'vencida', 'mal_estado'])->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};

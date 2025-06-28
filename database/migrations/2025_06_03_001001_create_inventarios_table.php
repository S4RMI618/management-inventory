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
        // Inventarios
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->foreignId('producto_id')->constrained();
            $table->decimal('cantidad', 15, 4)->default(0);
            $table->timestamps();

            $table->unique(['almacen_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};

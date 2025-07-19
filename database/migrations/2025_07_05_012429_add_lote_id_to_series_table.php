<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('series', function (Blueprint $table) {
            $table->unsignedBigInteger('lote_id')->nullable()->after('almacen_id');

            $table->foreign('lote_id')
                ->references('id')
                ->on('lotes')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropColumn('lote_id');
        });
    }
};

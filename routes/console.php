<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


use App\Models\Serie;
use App\Models\Lote;

Artisan::command('series:asignar-lote', function () {
    $series = Serie::whereNull('lote_id')->get();
    $total = 0;
    foreach ($series as $serie) {
        // Busca el lote por producto y número de lote
        $lote = Lote::where('producto_id', $serie->producto_id)
                    ->where('numero_lote', $serie->numero_lote)
                    ->first();
        if ($lote) {
            $serie->lote_id = $lote->id;
            $serie->save();
            $total++;
        }
    }
    $this->info("Lote_id asignado en $total series.");
});

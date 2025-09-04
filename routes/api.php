<?php

use DanielMonroy\SatEstadoCfdi\Http\Controllers\ConsultarEstadoCfdiController;
use Illuminate\Support\Facades\Route;

Route::post('/cfdi/estado', ConsultarEstadoCfdiController::class)->name('sat-estado.consultar');

Route::get('/cfdi/estatus', function () {
    return response()->json("El doc está activo, está vivo!");
})->name('sat-estado.estatus');

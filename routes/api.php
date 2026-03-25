<?php

use DanielMonroy\SatEstadoCfdi\Http\Controllers\ConsultarEstadoCfdiController;
use Illuminate\Support\Facades\Route;

Route::post('/cfdi/estado', ConsultarEstadoCfdiController::class)->name('sat-estado.consultar');

Route::get('/cfdi/estatus', fn () => response()->json([
    'ok' => true,
    'status' => 'available',
    'message' => 'El servicio del paquete está disponible.',
]))
    ->name('sat-estado.estatus');

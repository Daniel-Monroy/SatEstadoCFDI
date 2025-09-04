<?php

use DanielMonroy\SatEstadoCfdi\Http\Controllers\ConsultarEstadoCfdiController;
use Illuminate\Support\Facades\Route;

Route::post('/cfdi/estado', ConsultarEstadoCfdiController::class)->name('sat-estado.consultar');

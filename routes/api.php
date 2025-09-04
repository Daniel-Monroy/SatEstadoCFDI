<?php

use Illuminate\Support\Facades\Route;

Route::post('/cfdi/estado', \Controllers\ConsultarEstadoCfdiController::class)->name('sat-estado.consultar');

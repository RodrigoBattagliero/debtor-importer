<?php

use App\Http\Controllers\DebtorsController;
use App\Http\Controllers\InstitutionsController;
use Illuminate\Support\Facades\Route;

Route::get('/deudores/procesar-archivo', [DebtorsController::class, 'processFile']);
Route::get('/deudores/top/{n?}', [DebtorsController::class, 'top']);
Route::get('/deudores/{cuit}', [DebtorsController::class, 'get']);

Route::get('/entidades/{code}', [InstitutionsController::class, 'get']);


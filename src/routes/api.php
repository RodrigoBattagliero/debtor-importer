<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DebtorsController;
use App\Http\Controllers\ProcessFileController;
use App\Http\Controllers\InstitutionsController;

Route::post('/deudores/upload', [ImportController::class, 'upload']);

Route::get('/deudores/procesar-archivo', [ProcessFileController::class, 'processFile']);
Route::get('/deudores/top/{n?}', [DebtorsController::class, 'top']);
Route::get('/deudores/{cuit}', [DebtorsController::class, 'get']);
Route::get('/entidades/{code}', [InstitutionsController::class, 'get']);


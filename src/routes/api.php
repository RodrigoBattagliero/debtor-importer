<?php

use App\Http\Controllers\DebtorsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', [DebtorsController::class, 'index']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


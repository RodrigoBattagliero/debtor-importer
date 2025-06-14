<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DebtorsController;

Route::get('/test', [DebtorsController::class, 'index']);

Route::get('/welcome', function () {
    return view('welcome');
});



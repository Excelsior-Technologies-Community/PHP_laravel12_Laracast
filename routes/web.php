<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/larastan-playground', [CheckController::class, 'playground']);

Route::get('/test-service', [CheckController::class, 'test']);

Route::get('/dashboard', [CheckController::class, 'dashboard']);

Route::get('/check-type', [App\Http\Controllers\CheckController::class, 'checkStrictType']);

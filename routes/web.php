<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/gemini', [GeminiController::class, 'index'])->name('gemini.index');
Route::post('/gemini/generate', [GeminiController::class, 'generate'])->name('gemini.generate');
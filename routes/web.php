<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('clientes', ClienteController::class);

Route::resource('funcionarios', FuncionarioController::class);
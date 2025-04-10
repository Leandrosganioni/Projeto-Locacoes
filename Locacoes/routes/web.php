<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;

Route::get('/', function () {
    return view('index');
});

Route::resource('clientes', ClienteController::class);

Route::resource('funcionarios', FuncionarioController::class);

Route::resource('equipamentos', EquipamentoController::class);
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;

Route::get("/login", [AuthController::class, 'showFormLogin'])->name('login');
Route::post("/login", [AuthController::class, 'login']);

Route::middleware("auth")->group(function (){
    Route::resource("produtos", ProdutoController::class);
    Route::post("/logout", [AuthController::class, "logout"]);
});

Route::get('/', function () {
    return view('index');
});

Route::resource('clientes', ClienteController::class);

Route::resource('funcionarios', FuncionarioController::class);

Route::resource('equipamentos', EquipamentoController::class);
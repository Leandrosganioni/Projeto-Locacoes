<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PedidoController;

Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('/create-user', [AuthController::class, 'create'])->name('createUser');

Route::middleware("auth")->group(function () {
    Route::get('/index', function () {
        return view('index');
    })->name('index');

    Route::resource("clientes", ClienteController::class);
    Route::resource('funcionarios', FuncionarioController::class);
    Route::resource('equipamentos', EquipamentoController::class);
    Route::resource('pedidos', PedidoController::class);
    Route::resource('usuarios', UsuarioController::class);

    Route::post("/logout", [AuthController::class, "logout"])->name('logout');
});


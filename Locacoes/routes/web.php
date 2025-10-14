<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoItemController; 

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

    Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');


    Route::get('pedidos/{pedido}/decorridos', [PedidoController::class, 'decorridos'])->name('pedidos.decorridos');

    // Rota para servir dados JSON do gráfico de evolução do pedido
    Route::get('pedidos/{pedido}/grafico', [PedidoController::class, 'grafico'])->name('pedidos.grafico');


    Route::get('pedidos/{pedido}/comprovante', [PedidoController::class, 'comprovante'])->name('pedidos.comprovante');
    Route::resource('usuarios', UsuarioController::class);


    Route::prefix('pedidos/itens')->name('pedidos.itens.')->group(function () {
        Route::post('{item}/reservar', [PedidoItemController::class, 'reservar'])->name('reservar');
        Route::post('{item}/retirar',  [PedidoItemController::class, 'retirar'])->name('retirar');
        Route::post('{item}/devolver', [PedidoItemController::class, 'devolver'])->name('devolver');
        Route::post('{item}/cancelar', [PedidoItemController::class, 'cancelar'])->name('cancelar');
    });

    Route::post("/logout", [AuthController::class, "logout"])->name('logout');
});
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoItemController; 
use App\Http\Controllers\QuebraController;

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
    // Rota para exibir detalhes do pedido com itens e operações individuais
    // (utiliza o método show do resource controller)
    Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');

    // Rota para visualizar a evolução diária de valores dos itens de um pedido
    Route::get('pedidos/{pedido}/decorridos', [PedidoController::class, 'decorridos'])->name('pedidos.decorridos');
    Route::resource('usuarios', UsuarioController::class);

    //ações sobre itens de pedido (reservar/retirar/devolver/cancelar)
    Route::prefix('pedidos/itens')->name('pedidos.itens.')->group(function () {
        Route::post('{item}/reservar', [PedidoItemController::class, 'reservar'])->name('reservar');
        Route::post('{item}/retirar',  [PedidoItemController::class, 'retirar'])->name('retirar');
        Route::post('{item}/devolver', [PedidoItemController::class, 'devolver'])->name('devolver');
        Route::post('{item}/cancelar', [PedidoItemController::class, 'cancelar'])->name('cancelar');
    });

    // --- ROTAS DE QUEBRA E DEVOLUÇÃO ---
    Route::get('/quebras', [QuebraController::class, 'index'])->name('quebras.index');
    Route::get('/quebras/registrar/{pedido_id}', [QuebraController::class, 'create'])->name('quebras.create');
    Route::post('/quebras', [QuebraController::class, 'store'])->name('quebras.store');
    //FIM

    Route::post("/logout", [AuthController::class, "logout"])->name('logout');
});
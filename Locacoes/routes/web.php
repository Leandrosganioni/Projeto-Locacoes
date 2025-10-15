<?php

use Illuminate\Support\Facades\Route;

// Controllers de Autenticação
use App\Http\Controllers\AuthController;

// Controllers de Recursos (CRUD)
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoItemController; 

// Models para a página inicial
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Pedido;

/*
|--------------------------------------------------------------------------
| Rotas Web
|--------------------------------------------------------------------------
*/

// Rotas de Autenticação (Públicas)
Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('/create-user', [AuthController::class, 'create'])->name('createUser');

// Rotas Protegidas (Exigem Autenticação)
Route::middleware("auth")->group(function () {
    
    // Página Inicial (Dashboard)
    Route::get('/index', function () {
        $totalEquipamentos = Equipamento::count();
        $totalClientes = Cliente::count();
        $totalPedidos = Pedido::count();
        
        return view('index', compact('totalEquipamentos', 'totalClientes', 'totalPedidos'));
    })->name('index');

    // Rotas de Recursos (CRUDs)
    Route::resource("clientes", ClienteController::class);
    Route::resource('funcionarios', FuncionarioController::class);
    Route::resource('equipamentos', EquipamentoController::class);
    Route::resource('pedidos', PedidoController::class);
    Route::resource('usuarios', UsuarioController::class);

    // Rotas Específicas de Pedidos
    Route::get('pedidos/{pedido}/decorridos', [PedidoController::class, 'decorridos'])->name('pedidos.decorridos');
    Route::get('pedidos/{pedido}/grafico', [PedidoController::class, 'grafico'])->name('pedidos.grafico');
    Route::get('pedidos/{pedido}/comprovante', [PedidoController::class, 'comprovante'])->name('pedidos.comprovante');

    // Rotas para Ações nos Itens de um Pedido
    Route::prefix('pedidos/itens')->name('pedidos.itens.')->group(function () {
        Route::post('{item}/reservar', [PedidoItemController::class, 'reservar'])->name('reservar');
        Route::post('{item}/retirar',  [PedidoItemController::class, 'retirar'])->name('retirar');
        Route::post('{item}/devolver', [PedidoItemController::class, 'devolver'])->name('devolver');
        Route::post('{item}/cancelar', [PedidoItemController::class, 'cancelar'])->name('cancelar');
    });

    // Rota de Logout
    Route::post("/logout", [AuthController::class, "logout"])->name('logout');
});
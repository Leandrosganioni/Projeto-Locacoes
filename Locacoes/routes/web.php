<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoItemController;
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth; 


Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('/create-user', [AuthController::class, 'create'])->name('createUser');


Route::middleware("auth")->group(function () {


    Route::get('/index', function () {
        $user = Auth::user();


        if ($user->role === 'cliente') {
            $equipamentosDisponiveis = Equipamento::where('quantidade_disponivel', '>', 0)
                                                ->orderBy('nome')
                                                ->get(['id', 'nome', 'imagem', 'daily_rate']);
            return view('index_cliente', compact('equipamentosDisponiveis'));
        }


        $totalEquipamentos = Equipamento::count();
        $totalClientes = Cliente::count();
        $totalPedidos = Pedido::count();
        return view('index', compact('totalEquipamentos', 'totalClientes', 'totalPedidos'));

    })->name('index');


    Route::post("/logout", [AuthController::class, "logout"])->name('logout');

    

    Route::middleware('role:cliente,funcionario,admin')->group(function () {
        Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
        Route::get('pedidos/{pedido}/decorridos', [PedidoController::class, 'decorridos'])->name('pedidos.decorridos');
        Route::get('pedidos/{pedido}/grafico', [PedidoController::class, 'grafico'])->name('pedidos.grafico');
        Route::get('pedidos/{pedido}/comprovante', [PedidoController::class, 'comprovante'])->name('pedidos.comprovante');
    });

    

    Route::middleware('role:funcionario,admin')->group(function () {

        Route::resource("clientes", ClienteController::class);
        Route::resource('equipamentos', EquipamentoController::class);


        Route::resource('pedidos', PedidoController::class)->except(['index', 'show']); 

       
        Route::prefix('pedidos/itens')->name('pedidos.itens.')->group(function () {
            Route::post('{item}/reservar', [PedidoItemController::class, 'reservar'])->name('reservar');
            Route::post('{item}/retirar',  [PedidoItemController::class, 'retirar'])->name('retirar');
            Route::post('{item}/devolver', [PedidoItemController::class, 'devolver'])->name('devolver');
            Route::post('{item}/cancelar', [PedidoItemController::class, 'cancelar'])->name('cancelar');
        });
    });

    

    Route::middleware('role:admin')->group(function () {
        Route::resource('funcionarios', FuncionarioController::class);
        Route::resource('usuarios', UsuarioController::class); 
    });

});
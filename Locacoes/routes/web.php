<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoItemController;
use App\Http\Controllers\RelatorioController; 
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



    Route::middleware('role:funcionario,admin')->group(function () {
        
        Route::resource("clientes", ClienteController::class);
        Route::resource('equipamentos', EquipamentoController::class);

        //rotas para quebras e devoluções de funcionario e admin
        Route::get('/ocorrencias/registrar/{equipamento}', [\App\Http\Controllers\OcorrenciaEstoqueController::class, 'create'])
         ->name('ocorrencias.create');

        Route::post('/ocorrencias', [\App\Http\Controllers\OcorrenciaEstoqueController::class, 'store'])
         ->name('ocorrencias.store');

        // Rota para API interna (buscar pedidos do cliente via JS)
        Route::get('/api/clientes/{cliente}/pedidos', [\App\Http\Controllers\ClienteController::class, 'getPedidosPorCliente'])
         ->name('api.clientes.pedidos');
        //fim da rota de quebra

        Route::get('pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::post('pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
        Route::get('pedidos/{pedido}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
        Route::match(['put', 'patch'], 'pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
        Route::delete('pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');


        Route::prefix('pedidos/itens')->name('pedidos.itens.')->group(function () {
            Route::post('{item}/reservar', [PedidoItemController::class, 'reservar'])->name('reservar');
            Route::post('{item}/retirar',  [PedidoItemController::class, 'retirar'])->name('retirar');
            Route::post('{item}/devolver', [PedidoItemController::class, 'devolver'])->name('devolver');
            Route::post('{item}/cancelar', [PedidoItemController::class, 'cancelar'])->name('cancelar');
        });
    });


    Route::middleware('role:cliente,funcionario,admin')->group(function () {
        
        Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show'); 
        Route::get('pedidos/{pedido}/decorridos', [PedidoController::class, 'decorridos'])->name('pedidos.decorridos');
        Route::get('pedidos/{pedido}/grafico', [PedidoController::class, 'grafico'])->name('pedidos.grafico');
        Route::get('pedidos/{pedido}/comprovante', [PedidoController::class, 'comprovante'])->name('pedidos.comprovante');
    });
    

    Route::middleware('role:admin')->group(function () {
        Route::resource('funcionarios', FuncionarioController::class);
        Route::resource('usuarios', UsuarioController::class); 
        Route::get('/relatorios/quebras', [\App\Http\Controllers\RelatorioController::class, 'relatorioQuebras'])
            ->name('relatorios.quebras');
        
        
        // --- ROTAS DE RELATÓRIOS ---
        
        
        Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
        
        
        Route::get('/relatorios/estoque', [RelatorioController::class, 'relatorioEstoque'])->name('relatorios.estoque');

        
        Route::get('/relatorios/vendas', [RelatorioController::class, 'relatorioVendas'])->name('relatorios.vendas');
    });

});
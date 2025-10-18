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
use App\Models\PedidoProduto;

use Illuminate\Support\Facades\Artisan;


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


/*

Route::get('/recalcular-pedido/{id}', function ($id) {
    try {
        // --- INÍCIO DA ADIÇÃO ---
        // Força a limpeza de todas as caches do Laravel antes de executar
        Artisan::call('optimize:clear');
        Log::info('Cache limpa via rota de recálculo.');
        // --- FIM DA ADIÇÃO ---

        $pedido = \App\Models\Pedido::findOrFail($id);
        $itensRecalculados = 0;
        $totalAntigo = 0;
        $totalNovo = 0;

        foreach ($pedido->itens as $item) {
            if ($item->status === \App\Models\PedidoProduto::STATUS_DEVOLVIDO) {
                $totalAntigo += $item->computed_total;

                // Chama a função recalcularValorDevolvido()
                // que agora usará o código 100% atualizado de calcularValor()
                $item->recalcularValorDevolvido();

                // Recarrega o item da base de dados
                $item->refresh(); 

                $totalNovo += $item->computed_total;
                $itensRecalculados++;
            }
        }

        if ($itensRecalculados > 0) {
            return response()->json([
                'status' => 'Sucesso (Cache Limpa)',
                'pedido_id' => $pedido->id,
                'itens_recalculados' => $itensRecalculados,
                'total_antigo' => $totalAntigo,
                'total_novo' => $totalNovo,
            ]);
        } else {
            // ... (o resto da rota continua igual) ...
            return response()->json([
                'status' => 'Nada a fazer',
                'mensagem' => 'Nenhum item devolvido encontrado para este pedido.',
                'pedido_id' => $pedido->id,
            ]);
        }

    } catch (\Exception $e) {
        return response()->json(['status' => 'Erro', 'mensagem' => $e->getMessage()], 500);
    }
})->middleware('auth');
// --- FIM DA ROTA TEMPORÁRIA ---
*/
<?php
namespace App\Http\Controllers;

use App\Models\PedidoProduto;
use Illuminate\Http\RedirectResponse;

class PedidoItemController extends Controller
{
    public function reservar(PedidoProduto $item): RedirectResponse
    {
        $item->reservar();
        $this->fecharPedidoSeNecessario($item);
        return back();
    }

    public function retirar(PedidoProduto $item): RedirectResponse
    {
        $item->retirar();
        $this->fecharPedidoSeNecessario($item);
        return back();
    }

    public function devolver(PedidoProduto $item): RedirectResponse
    {
        $item->devolver();
        $this->fecharPedidoSeNecessario($item);
        return back();
    }

    public function cancelar(PedidoProduto $item): RedirectResponse
    {
        $item->cancelar();
        $this->fecharPedidoSeNecessario($item);
        return back();
    }

    protected function fecharPedidoSeNecessario(PedidoProduto $item): void
    {
        $pedido = $item->pedido()->with('itens')->first();
        if (!$pedido) return;

        $ativos = $pedido->itens()->whereIn('status', [
            PedidoProduto::STATUS_RESERVADO,
            PedidoProduto::STATUS_EM_LOCACAO
        ])->count();

        if ($ativos === 0) {
            // Quando não há itens ativos, o pedido está encerrado. Se desejar
            // registrar um status, adicione uma coluna 'status' na tabela
            // 'pedidos' e atualize aqui. Caso contrário, apenas mantenha o
            // fechamento lógico baseado na contagem de itens.
            // $pedido->status = 'fechado';
            // $pedido->save();
        }
    }
}

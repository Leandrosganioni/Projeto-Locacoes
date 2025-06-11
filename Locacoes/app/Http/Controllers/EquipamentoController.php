<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use Illuminate\Http\Request;

class EquipamentoController extends Controller
{
    public function index()
    {
        $equipamentos = Equipamento::all();
        return view('equipamentos.index', compact('equipamentos'));
    }

    public function create()
    {
        return view('equipamentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'tipo' => 'required',
            'quantidade' => 'required|integer',
            'descricao_tecnica' => 'required',
            'informacoes_manutencao' => 'required',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagem')) {
            $arquivo = $request->file('imagem');
            $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();
            $arquivo->move(public_path('images/equipamentos'), $nomeArquivo);
            $data['imagem'] = $nomeArquivo;
        }

        Equipamento::create($data);

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento cadastrado com sucesso!');
    }


    public function show(Equipamento $equipamento)
    {
        return view('equipamentos.show', compact('equipamento'));
    }

    public function edit(Equipamento $equipamento)
    {
        return view('equipamentos.edit', compact('equipamento'));
    }

    public function update(Request $request, Equipamento $equipamento)
    {
        $request->validate([
            'nome' => 'required|string|max:150',
            'tipo' => 'required|string|max:50',
            'quantidade' => 'required|integer',
            'descricao_tecnica' => 'required|string',
            'informacoes_manutencao' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagem')) {

            if ($equipamento->imagem && file_exists(public_path('images/equipamentos/' . $equipamento->imagem))) {
                unlink(public_path('images/equipamentos/' . $equipamento->imagem));
            }

            $arquivo = $request->file('imagem');
            $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();
            $arquivo->move(public_path('images/equipamentos'), $nomeArquivo);
            $data['imagem'] = $nomeArquivo;
        }

        $equipamento->update($data);

        return redirect()->route('equipamentos.index')
            ->with('success', 'Equipamento atualizado com sucesso!');
    }


    public function destroy(Equipamento $equipamento)
    {
        try {
            $equipamento->delete();
            return redirect()->route('equipamentos.index')
                ->with('success', 'Equipamento excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('equipamentos.index')
                ->with('error', 'Erro ao excluir Equipamento: ' . $e->getMessage());
        }
    }
}

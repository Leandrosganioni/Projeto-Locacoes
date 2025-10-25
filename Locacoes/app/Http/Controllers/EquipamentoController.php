<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EquipamentoController extends Controller
{
    public function index()
    {
        $equipamentos = Equipamento::orderBy('nome')->get();
        return view('equipamentos.index', compact('equipamentos'));
    }

    public function create()
    {
        return view('equipamentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'                   => 'required|string|max:150',
            'tipo'                   => 'required|string|max:50',
            'daily_rate'             => 'required|numeric|min:0',
            'quantidade_total'       => 'required|integer|min:0',
            'descricao_tecnica'      => 'required|string',
            'informacoes_manutencao' => 'required|string',
            'imagem'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'nome',
            'tipo',
            'daily_rate',
            'quantidade_total',
            'descricao_tecnica',
            'informacoes_manutencao',
        ]);

        // disponibilidade inicial = total
        $data['quantidade_disponivel'] = (int)$request->quantidade_total;

        // legado (se a coluna 'quantidade' existir, mantém espelhado)
        if (\Schema::hasColumn('equipamentos', 'quantidade')) {
            $data['quantidade'] = (int)$request->quantidade_total;
        }

        if ($request->hasFile('imagem')) {
            $dir = public_path('images/equipamentos');
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $file = $request->file('imagem');
            $nome = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move($dir, $nome);
            $data['imagem'] = $nome;
        }

        Equipamento::create($data);

        return redirect()->route('equipamentos.index')->with('success', 'Equipamento criado com sucesso!');
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
            'nome'                   => 'required|string|max:150',
            'tipo'                   => 'required|string|max:50',
            'daily_rate'             => 'required|numeric|min:0',
            'quantidade_total'       => 'required|integer|min:0',
            'descricao_tecnica'      => 'required|string',
            'informacoes_manutencao' => 'required|string',
            'imagem'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'nome',
            'tipo',
            'daily_rate',
            'quantidade_total',
            'descricao_tecnica',
            'informacoes_manutencao',
        ]);


        if (\Schema::hasColumn('equipamentos', 'quantidade')) {
            $data['quantidade'] = (int)$request->quantidade_total;
        }

        if ($request->hasFile('imagem')) {
            $dir = public_path('images/equipamentos');
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            if ($equipamento->imagem) {
                $antigo = $dir . DIRECTORY_SEPARATOR . $equipamento->imagem;
                if (is_file($antigo)) @unlink($antigo);
            }

            $file = $request->file('imagem');
            $nome = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move($dir, $nome);
            $data['imagem'] = $nome;
        }

        $equipamento->update($data);

        return redirect()->route('equipamentos.index')->with('success', 'Equipamento atualizado com sucesso!');
    }


    public function destroy(Equipamento $equipamento)
    {
        // apaga arquivo físico, se existir
        if ($equipamento->imagem) {
            $path = public_path('images/equipamentos/'. $equipamento->imagem);
            if (is_file($path)) @unlink($path);
        }

        $equipamento->delete();

        return redirect()->route('equipamentos.index')->with('success', 'Equipamento excluído com sucesso!');
    }
}

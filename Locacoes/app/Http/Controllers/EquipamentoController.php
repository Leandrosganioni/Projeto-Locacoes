<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use Illuminate\Http\Request;

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
            'nome' => 'required|string|max:150',
            'tipo' => 'required|string|max:50',
            'quantidade' => 'required|integer|min:1',
            'descricao_tecnica' => 'required|string',
            'informacoes_manutencao' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'nome','tipo','quantidade','descricao_tecnica','informacoes_manutencao'
        ]);

        if ($request->hasFile('imagem')) {
            $dir = public_path('images/equipamentos');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $file = $request->file('imagem');
            $nome = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move($dir, $nome);

            // Banco guarda só o nome (ex.: "1694987456_foto.jpg")
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
            'nome' => 'required|string|max:150',
            'tipo' => 'required|string|max:50',
            'quantidade' => 'required|integer|min:1',
            'descricao_tecnica' => 'required|string',
            'informacoes_manutencao' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'nome','tipo','quantidade','descricao_tecnica','informacoes_manutencao'
        ]);

        if ($request->hasFile('imagem')) {
            $dir = public_path('images/equipamentos');
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            // remove antiga
            if ($equipamento->imagem) {
                $antigo = $dir.DIRECTORY_SEPARATOR.$equipamento->imagem;
                if (is_file($antigo)) @unlink($antigo);
            }

            $file = $request->file('imagem');
            $nome = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
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

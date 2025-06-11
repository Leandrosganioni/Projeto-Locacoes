<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showFormLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credenciais = $request->only('email', 'password');

        $funcionario = \App\Models\Funcionario::where('email', $credenciais['email'])->first();

        if (!$funcionario || $funcionario->nivel_acesso === 'FUNCIONARIO') {
            return back()->withErrors([
                'login' => 'Você não tem permissão para acessar o sistema.',
            ]);
        }

        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();
            return redirect()->intended('/index');
        }

        return back()->withErrors([
            'login' => 'Credenciais inválidas!'
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    public function showFormRegister()
    {
        return view('register');
    }

    public function create(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ],
            [
                'name.required' => '',
            ]
        );

        $usuario = new Funcionario();

        $usuario->nome = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->password = bcrypt($validated['password']);
        $usuario->nivel_acesso = 'COLABORADOR'; // ou 'ADMINISTRADOR'

        $usuario->save();


        return redirect('/index');
    }
}

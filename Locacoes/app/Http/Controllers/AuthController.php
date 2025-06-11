<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showFormLogin(){
        return view('login');
    }

    public function login(Request $request){
        $credenciais = $request->only('email', 'password');

        if (Auth::attempt($credenciais)){
            $request->session()->regenerate();
            return redirect()->intended('/index');
        }

        return back()->withErrors([
            'login' => 'Credenciais inválidas!'
        ]);
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    public function showFormRegister()
    {
        return view('register');
    }

    public function create(Request $request){
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

        $usuario = new User();

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->password = bcrypt($validated['password']);

        $usuario->save();

        return redirect('/index');
    }

}
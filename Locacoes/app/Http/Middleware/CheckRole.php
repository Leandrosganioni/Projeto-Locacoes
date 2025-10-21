<?php

namespace App\Http\Middleware; 

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole 
{
    /**
     * Handle an incoming request.
     * (Lida com uma requisição que está a chegar)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  // Aceita um ou mais papéis (ex: 'admin', 'funcionario')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verifica se o utilizador está logado
        // Se não estiver logado, redireciona para a página de login.
        if (!Auth::check()) {
            return redirect('login');
        }

        // 2. Obtém o utilizador logado
        $user = Auth::user();

        // 3. Verifica se o papel (role) do utilizador está na lista de papéis permitidos ($roles)
        if (in_array($user->role, $roles)) {
            // 4. Se o papel for permitido, deixa a requisição continuar
            return $next($request);
        }

        // 5. Se o utilizador está logado, mas NÃO TEM o papel (role) correto:
        //    Redireciona para a página inicial ('/') com uma mensagem de erro.
        return redirect('/')->with('error', 'Você não tem permissão para acessar esta página.');
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNivelAcesso
{
    public function handle(Request $request, Closure $next, ...$niveisPermitidos)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->nivel_acesso, $niveisPermitidos)) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}

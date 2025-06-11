<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade; // Importe o Blade
use Illuminate\Support\Facades\Auth; // Importe o Auth

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Diretiva para verificar se o usuário é Administrador
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->nivel_acesso == 'ADMINISTRADOR';
        });

        // Diretiva para verificar se o usuário é Colaborador OU Administrador
        // (Um admin também pode fazer o que um colaborador faz)
        Blade::if('colaborador', function () {
            if (!Auth::check()) {
                return false;
            }
            $nivel = Auth::user()->nivel_acesso;
            return $nivel == 'COLABORADOR' || $nivel == 'ADMINISTRADOR';
        });
    }
}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ELoc locações</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles') 
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('index') }}">ELoc locações</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('index') ? 'active' : '' }}" href="{{ route('index') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('funcionarios.*') ? 'active' : '' }}" href="{{ route('funcionarios.index') }}">Funcionários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('equipamentos.*') ? 'active' : '' }}" href="{{ route('equipamentos.index') }}">Equipamentos</a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <span class="text-white me-3">Usuário: {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm" type="submit">Sair</button>
                </form>
            </div>
        </div>
    </div>
    </nav>



    <!-- Conteúdo Principal -->

    

    <main class="container py-4">
        @yield('content')
    </main>

   
    <footer class="footer bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} Sistema de Gestão. Todos os direitos reservados.</p>
        </div>
    </footer>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts') 
</body>
</html>
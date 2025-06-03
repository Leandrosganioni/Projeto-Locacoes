<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ELoc locações</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('index') }}">ELoc locações</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('funcionarios.*') ? 'active' : '' }}" href="{{ route('funcionarios.index') }}">Funcionários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('equipamentos.*') ? 'active' : '' }}" href="{{ route('equipamentos.index') }}">Equipamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}" href="{{ route('pedidos.index') }}">Pedidos</a>
                    </li>
                </ul>

                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center px-2 py-1 rounded" type="button" data-bs-toggle="dropdown" aria-expanded="false">

                        <span class="fw-bold me-2">{{ Auth::user()->name }}</span>
                        <i class="bi bi-caret-down-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @yield('content')
    </main>

    <footer class="footer bg-dark text-white text-center py-3">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} Sistema para fins acadêmico. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    
</body>

</html>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ELoc Locações</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>


<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg bg-body border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ route('index') }}">ELoc Locações</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Abrir navegação">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-2">

                    {{-- Clientes: Visível para Colaboradores e Admins --}}
                    @colaborador
                    <li class="nav-item">
                        <a class="nav-link ... " href="{{ route('clientes.index') }}">
                            <i class="bi bi-people"></i> Clientes
                        </a>
                    </li>
                    @endcolaborador

                    {{-- Funcionários: Visível APENAS para Admins --}}
                    @admin
                    <li class="nav-item">
                        <a class="nav-link ..." href="{{ route('funcionarios.index') }}">
                            <i class="bi bi-person-badge"></i> Funcionários
                        </a>
                    </li>
                    @endadmin

                    {{-- Equipamentos: Visível APENAS para Admins --}}
                    @colaborador
                    <li class="nav-item">
                        <a class="nav-link ..." href="{{ route('equipamentos.index') }}">
                            <i class="bi bi-hammer"></i> Equipamentos
                        </a>
                    </li>
                    @endcolaborador

                    {{-- Pedidos: Visível para Colaboradores e Admins --}}
                    @colaborador
                    <li class="nav-item">
                        <a class="nav-link ..." href="{{ route('pedidos.index') }}">
                            <i class="bi bi-box"></i> Pedidos
                        </a>
                    </li>
                    @endcolaborador

                </ul>

                @auth
                {{-- Este trecho só será exibido se o usuário ESTIVER LOGADO --}}
                <div class="dropdown ms-3">
                    <button class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-1 rounded-pill"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="fw-medium">{{ Auth::user()->nome }}</span>
                        <i class="bi bi-caret-down-fill"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">
                        <li>
                            <a class="dropdown-item" href="{{ route('conta.show') }}">
                                <i class="bi bi-person-lines-fill me-1"></i> Meus Dados
                            </a>
                        </li>


                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Sair</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth

                @guest
                {{-- Este trecho é exibido para VISITANTES (não logados) --}}
                <div class="ms-3">
                    <a href="{{ route('login') }}" class="btn btn-primary">Entrar</a>
                </div>
                @endguest
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @yield('content')
    </main>

    <footer class="text-center py-4 bg-transparent text-muted small">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} ELoc Rentals. All rights reserved.</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>

</html>
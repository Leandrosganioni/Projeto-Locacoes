<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ELoc Locações</title>
    {{-- A classe Auth será usada para verificar os papéis --}}
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp
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

                    {{-- --- Documentação (Links Dinâmicos) --- --}}

                    {{-- Link Clientes: Visível apenas para Funcionários e Admins --}}
                    @if(in_array(Auth::user()->role, ['funcionario', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1 {{ request()->routeIs('clientes.*') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('clientes.index') }}">
                                <i class="bi bi-people"></i> Clientes
                            </a>
                        </li>
                    @endif

                    {{-- Link Funcionários: Visível apenas para Admins --}}
                    @if(Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1 {{ request()->routeIs('funcionarios.*') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('funcionarios.index') }}">
                                <i class="bi bi-person-badge"></i> Funcionários
                            </a>
                        </li>
                    @endif

                    {{-- Link Equipamentos: Visível apenas para Funcionários e Admins --}}
                    @if(in_array(Auth::user()->role, ['funcionario', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1 {{ request()->routeIs('equipamentos.*') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('equipamentos.index') }}">
                                <i class="bi bi-hammer"></i> Equipamentos
                            </a>
                        </li>
                    @endif
                    
                    {{-- Link Pedidos: Visível para Todos (Clientes, Funcionários, Admins) --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-1 {{ request()->routeIs('pedidos.*') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('pedidos.index') }}">
                            <i class="bi bi-box"></i> Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-1 {{ request()->routeIs('quebras.*') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('quebras.index') }}">
                            <i class="bi bi-wrench-adjustable"></i> Reg. Quebra
                        </a>
                    </li>
                
                    {{-- O link 'Cadastrar Usuário' foi removido pois a criação de usuários
                         agora está integrada ao cadastro de funcionários (para admins)
                         e clientes (para funcionários/admins). --}}
                </ul>

                {{-- Dropdown do Usuário (Visível para todos logados) --}}
                <div class="dropdown ms-3">
                    <button class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-1 rounded-pill" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Abrir menu de usuário">
                        <i class="bi bi-person-circle fs-5"></i>
                        {{-- Exibe o nome do usuário logado --}}
                        <span class="fw-medium">{{ Auth::user()->name }}</span>
                        <i class="bi bi-caret-down-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">
                        <li>
                            <h6 class="dropdown-header">
                                {{ Auth::user()->name }}
                                <small class="d-block text-muted fw-normal">{{ Auth::user()->email }}</small>
                            </h6>
                        </li>
                         <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="dropdown-item text-danger d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    {{-- Conteúdo principal --}}
    <main class="container py-4 flex-grow-1">
        
        {{-- Exibe a mensagem de erro do middleware (Acesso não autorizado) --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Rodapé --}}
    <footer class="text-center py-4 bg-transparent text-muted small">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} ELoc Rentals. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>

</html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Locação</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 5rem 0;
            margin-bottom: 3rem;
        }
        .feature-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">ELoc locações</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clientes.index') }}">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('funcionarios.index') }}">Funcionários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('equipamentos.index') }}">Equipamentos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4">Bem-vindo ao ELoc locações</h1>
            <p class="lead">Uma solução completa para locar seus equipamentos!</p>
            <a href="#features" class="btn btn-primary btn-lg mt-3">Conheça mais</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="container mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Gerenciador de Clientes</h5>
                        <p class="card-text">Crud de todos os clientes.</p>
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-primary">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Gerenciador de Funcionários</h5>
                        <p class="card-text">Crud de todos os funcionários da empresa.</p>
                        <a href="{{ route('funcionarios.index') }}" class="btn btn-outline-primary">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Gerenciador de Equipamentos</h5>
                        <p class="card-text">crud de todos os equipamentos da organização.</p>
                        <a href="{{ route('equipamentos.index') }}" class="btn btn-outline-primary">Acessar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} Sistema de Locação. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
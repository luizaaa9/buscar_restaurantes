<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Restaurant Reviews</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --burgundy: #800020;
            --dark-burgundy: #600018;
            --black: #1C1C1C;
            --dark-gray: #2D2D2D;
            --medium-gray: #3C3C3C;
            --light-gray: #E8E8E8;
        }
        
        body {
            background-color: var(--black);
            color: var(--light-gray);
            min-height: 100vh;
        }
        
        .navbar {
            background-color: var(--burgundy) !important;
            box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .nav-link {
            border-radius: 4px;
            padding: 8px 16px !important;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.15);
            border-color: rgba(40, 167, 69, 0.3);
            color: #d4edda;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.15);
            border-color: rgba(220, 53, 69, 0.3);
            color: #f8d7da;
        }
        
        footer {
            background-color: var(--dark-gray) !important;
            border-top: 3px solid var(--burgundy);
            margin-top: auto;
        }
        
        main {
            flex: 1;
        }
        
        .btn-primary {
            background-color: var(--burgundy);
            border-color: var(--burgundy);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-burgundy);
            border-color: var(--dark-burgundy);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--burgundy);
            border-color: var(--burgundy);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--burgundy);
            border-color: var(--burgundy);
        }
        
        .badge-primary {
            background-color: var(--burgundy);
        }
        
        .card {
            background-color: var(--dark-gray);
            border: 1px solid var(--medium-gray);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .card-header {
            background-color: var(--medium-gray);
            border-bottom: 1px solid var(--burgundy);
        }
        
        .form-control, .form-select {
            background-color: var(--medium-gray);
            border: 1px solid #444;
            color: var(--light-gray);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--medium-gray);
            border-color: var(--burgundy);
            box-shadow: 0 0 0 0.25rem rgba(128, 0, 32, 0.25);
            color: var(--light-gray);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <div class="bg-white rounded-circle p-2 me-3">
                    <i class="fas fa-utensils text-burgundy"></i>
                </div>
                <span>Restaurant Reviews</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-2">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('restaurants.map') || request()->routeIs('home') ? 'active' : '' }}" 
                           href="{{ route('restaurants.map') }}">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            <span class="d-none d-lg-inline">Mapa</span>
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('restaurants.index') ? 'active' : '' }}" 
                           href="{{ route('restaurants.index') }}">
                            <i class="fas fa-list me-2"></i>
                            <span class="d-none d-lg-inline">Lista</span>
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('restaurants.create') ? 'active' : '' }}" 
                           href="{{ route('restaurants.create') }}">
                            <i class="fas fa-plus me-2"></i>
                            <span class="d-none d-lg-inline">Cadastrar</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 mx-3" role="alert">
                <div class="container">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-lg"></i>
                        <div class="flex-grow-1">{{ session('success') }}</div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 mx-3" role="alert">
                <div class="container">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                        <div class="flex-grow-1">{{ session('error') }}</div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-4 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-burgundy rounded-circle p-2 me-3">
                            <i class="fas fa-utensils text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Restaurant Reviews</h5>
                            <p class="mb-0 text-light-gray">Encontre os melhores restaurantes e compartilhe suas experiências.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-end gap-3">
                        <div class="color-palette d-flex gap-2">
                            <div class="color-swatch" style="background-color: #800020; width: 20px; height: 20px; border-radius: 3px;"></div>
                            <div class="color-swatch" style="background-color: #1C1C1C; width: 20px; height: 20px; border-radius: 3px; border: 1px solid #444;"></div>
                        </div>
                        <p class="mb-0">&copy; 2024 Restaurant Reviews</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
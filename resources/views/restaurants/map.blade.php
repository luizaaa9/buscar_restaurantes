<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Restaurant Reviews</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .star-rating { color: #ffc107; }
        .restaurant-card { transition: transform 0.2s; }
        .restaurant-card:hover { transform: translateY(-5px); }
        .navbar-brand { font-weight: 600; }
        
        /* Corrigir ícones do Leaflet */
        .leaflet-default-icon-path {
            background-image: url(https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png);
        }
        .leaflet-default-shadow-path {
            background-image: url(https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-utensils"></i> Restaurant Reviews
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('restaurants.map') || request()->routeIs('home') ? 'active' : '' }}" 
                           href="{{ route('restaurants.map') }}">
                            <i class="fas fa-map-marked-alt"></i> Mapa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('restaurants.index') ? 'active' : '' }}" 
                           href="{{ route('restaurants.index') }}">
                            <i class="fas fa-list"></i> Lista
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('restaurants.create') ? 'active' : '' }}" 
                           href="{{ route('restaurants.create') }}">
                            <i class="fas fa-plus"></i> Cadastrar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-utensils"></i> Restaurant Reviews</h5>
                    <p class="mb-0">Encontre os melhores restaurantes e compartilhe suas experiências.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2024 Restaurant Reviews. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
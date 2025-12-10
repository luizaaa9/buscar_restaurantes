@extends('layouts.app')

@section('title', 'Lista de Restaurantes')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-burgundy mb-2">
                        <i class="fas fa-utensils me-2"></i> 
                        Todos os Restaurantes
                    </h1>
                    <p class="text-muted">Explore nossa seleção de restaurantes cadastrados</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('restaurants.map') }}" class="btn btn-outline-burgundy">
                        <i class="fas fa-map-marked-alt me-1"></i> Ver Mapa
                    </a>
                    <a href="{{ route('restaurants.create') }}" class="btn btn-burgundy">
                        <i class="fas fa-plus me-1"></i> Cadastrar Restaurante
                    </a>
                </div>
            </div>

            <!-- Search Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-search me-2"></i>Buscar Restaurantes
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('restaurants.search') }}" method="GET" id="searchForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-light-gray">Buscar por:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark-gray border-dark">
                                        <i class="fas fa-search text-burgundy"></i>
                                    </span>
                                    <input type="text" name="query" class="form-control border-dark" 
                                           placeholder="Nome, descrição ou endereço..." 
                                           value="{{ request('query') }}"
                                           id="searchInput">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-light-gray">Tipo de Cozinha:</label>
                                <select name="cuisine" class="form-select border-dark" id="cuisineSelect">
                                    <option value="">Todos os tipos</option>
                                    @foreach(['Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa', 'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar'] as $cuisine)
                                        <option value="{{ $cuisine }}" {{ request('cuisine') == $cuisine ? 'selected' : '' }}>
                                            {{ $cuisine }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-burgundy w-100 h-100">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Filters -->
            @if(request()->has('query') || request()->has('cuisine'))
            <div class="alert alert-dark border-burgundy mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-filter me-2 text-burgundy"></i>
                        @if(request('query') && request('cuisine'))
                            Filtros ativos: "<strong>{{ request('query') }}</strong>" e cozinha "<strong>{{ request('cuisine') }}</strong>"
                        @elseif(request('query'))
                            Buscando por: "<strong>{{ request('query') }}</strong>"
                        @elseif(request('cuisine'))
                            Filtrando por cozinha: "<strong>{{ request('cuisine') }}</strong>"
                        @endif
                    </div>
                    <a href="{{ route('restaurants.index') }}" class="btn btn-sm btn-outline-burgundy">
                        <i class="fas fa-times me-1"></i> Limpar Filtros
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Restaurants Grid -->
    @if($restaurants->count() > 0)
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="restaurantsGrid">
        @foreach($restaurants as $restaurant)
        <div class="col">
            <div class="card restaurant-card h-100 border-0 shadow-sm" 
                 onclick="window.location.href='{{ route('restaurants.show', $restaurant->id) }}'"
                 style="cursor: pointer;">
                
                <!-- Photo Section -->
                <div class="position-relative">
                    @if(!empty($restaurant->photos) && count($restaurant->photos) > 0)
                        @php
                            $firstPhoto = $restaurant->photos[0];
                            $photoUrl = is_array($firstPhoto) ? $firstPhoto['url'] : (is_string($firstPhoto) ? $firstPhoto : '');
                        @endphp
                        <img src="{{ $photoUrl }}" 
                             class="card-img-top restaurant-image" 
                             alt="{{ $restaurant->name }}"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';">
                    @else
                        <div class="card-img-top bg-dark-gray d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-utensils fa-4x text-medium-gray"></i>
                        </div>
                    @endif
                    
                    <!-- Rating Badge -->
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-burgundy px-3 py-2 d-flex align-items-center">
                            <i class="fas fa-star me-1"></i>
                            <span>{{ number_format($restaurant->average_rating, 1) }}</span>
                        </span>
                    </div>
                    
                    <!-- Total Photos Badge -->
                    @if(!empty($restaurant->photos) && count($restaurant->photos) > 1)
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge bg-dark bg-opacity-75 px-2 py-1">
                            <i class="fas fa-camera me-1"></i>{{ count($restaurant->photos) }}
                        </span>
                    </div>
                    @endif
                </div>
                
                <!-- Card Body -->
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-white mb-2">{{ $restaurant->name }}</h5>
                    
                    <!-- Rating Stars -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating-stars me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($restaurant->average_rating))
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $restaurant->average_rating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-medium-gray"></i>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">({{ $restaurant->total_reviews }} avaliações)</small>
                        </div>
                        <p class="card-text text-light-gray small mb-3">{{ Str::limit($restaurant->description, 80) }}</p>
                    </div>

                    <!-- Cuisine Tags -->
                    <div class="mb-3">
                        @if(!empty($restaurant->cuisine_types))
                            @foreach(array_slice($restaurant->cuisine_types, 0, 2) as $type)
                                <span class="badge bg-medium-gray text-light-gray me-1 mb-1">{{ $type }}</span>
                            @endforeach
                            @if(count($restaurant->cuisine_types) > 2)
                                <span class="badge bg-dark-gray text-light-gray">+{{ count($restaurant->cuisine_types) - 2 }}</span>
                            @endif
                        @endif
                    </div>

                    <!-- Address and Action -->
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted text-truncate me-2">
                                <i class="fas fa-map-marker-alt me-1 text-burgundy"></i> 
                                {{ Str::limit($restaurant->address, 25) }}
                            </small>
                            <button class="btn btn-sm btn-outline-burgundy"
                                    onclick="event.stopPropagation(); window.location.href='{{ route('restaurants.show', $restaurant->id) }}'">
                                <i class="fas fa-eye me-1"></i> Ver
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Hover Overlay -->
                <div class="card-hover-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-external-link-alt fa-2x"></i>
                        <p class="mt-2 mb-0">Clique para ver detalhes</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @else
    <div class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-utensils fa-5x text-medium-gray mb-4"></i>
            <h3 class="text-light-gray mb-3">Nenhum restaurante encontrado</h3>
            <p class="text-muted mb-4">
                @if(request()->has('query') || request()->has('cuisine'))
                    Tente ajustar os filtros de busca ou limpe os filtros para ver todos os restaurantes.
                @else
                    Seja o primeiro a cadastrar um restaurante!
                @endif
            </p>
            <div class="d-flex justify-content-center gap-3">
                @if(request()->has('query') || request()->has('cuisine'))
                    <a href="{{ route('restaurants.index') }}" class="btn btn-outline-burgundy">
                        <i class="fas fa-times me-2"></i> Limpar Filtros
                    </a>
                @endif
                <a href="{{ route('restaurants.create') }}" class="btn btn-burgundy">
                    <i class="fas fa-plus me-2"></i> Cadastrar Restaurante
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
:root {
    --black: #000000;
    --dark-gray: #1a1a1a;
    --medium-gray: #333333;
    --light-gray: #cccccc;
    --burgundy: #800020;
    --dark-burgundy: #600018;
}

.restaurant-card {
    transition: all 0.3s ease;
    background: var(--dark-gray);
    border: 1px solid var(--medium-gray);
    position: relative;
    overflow: hidden;
}

.restaurant-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(128, 0, 32, 0.15) !important;
    border-color: var(--burgundy);
}

.restaurant-image {
    height: 200px;
    width: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.restaurant-card:hover .restaurant-image {
    transform: scale(1.05);
}

.rating-stars {
    color: #ffc107;
}

.rating-stars i {
    margin-right: 2px;
}

.card-hover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(128, 0, 32, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: inherit;
}

.restaurant-card:hover .card-hover-overlay {
    opacity: 1;
}

.overlay-content {
    text-align: center;
    color: white;
    padding: 20px;
}

.overlay-content i {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.bg-dark-gray {
    background-color: var(--dark-gray) !important;
}

.bg-medium-gray {
    background-color: var(--medium-gray) !important;
}

.text-medium-gray {
    color: var(--medium-gray) !important;
}

.text-light-gray {
    color: var(--light-gray) !important;
}

.btn-burgundy {
    background-color: var(--burgundy);
    border-color: var(--burgundy);
    color: white;
    transition: all 0.3s ease;
}

.btn-burgundy:hover {
    background-color: var(--dark-burgundy);
    border-color: var(--dark-burgundy);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
}

.btn-outline-burgundy {
    color: var(--burgundy);
    border-color: var(--burgundy);
    transition: all 0.3s ease;
}

.btn-outline-burgundy:hover {
    background-color: var(--burgundy);
    border-color: var(--burgundy);
    color: white;
    transform: translateY(-2px);
}

.empty-state {
    max-width: 500px;
    margin: 0 auto;
    padding: 3rem;
    background: var(--dark-gray);
    border-radius: 12px;
    border: 2px dashed var(--medium-gray);
}

/* Custom Scrollbar */
#restaurantsGrid::-webkit-scrollbar {
    width: 8px;
}

#restaurantsGrid::-webkit-scrollbar-track {
    background: var(--dark-gray);
    border-radius: 4px;
}

#restaurantsGrid::-webkit-scrollbar-thumb {
    background: var(--burgundy);
    border-radius: 4px;
}

#restaurantsGrid::-webkit-scrollbar-thumb:hover {
    background: var(--dark-burgundy);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .restaurant-image {
        height: 150px;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cuisineSelect')?.addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });
    const cards = document.querySelectorAll('.restaurant-card');
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.btn')) {
                window.location.href = this.dataset.href || 
                    this.querySelector('a')?.href || 
                    this.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
            }
        });
    });
    
    document.getElementById('searchInput')?.focus();
});
</script>
@endpush
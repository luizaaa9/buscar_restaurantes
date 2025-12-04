@extends('layouts.app')

@section('title', 'Lista de Restaurantes')

@section('content')
<div class="container">
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
                <div>
                    <a href="{{ route('restaurants.map') }}" class="btn btn-outline-burgundy me-2">
                        <i class="fas fa-map-marked-alt"></i> Ver Mapa
                    </a>
                    <a href="{{ route('restaurants.create') }}" class="btn btn-burgundy">
                        <i class="fas fa-plus"></i> Cadastrar
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-search me-2"></i>Buscar Restaurantes
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('restaurants.search') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Buscar por:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark-gray border-dark">
                                        <i class="fas fa-search text-burgundy"></i>
                                    </span>
                                    <input type="text" name="query" class="form-control border-dark" 
                                           placeholder="Nome, descrição ou endereço..." 
                                           value="{{ request('query') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tipo de Cozinha:</label>
                                <select name="cuisine" class="form-select border-dark">
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

            @if(request()->has('query') || request()->has('cuisine'))
            <div class="alert alert-dark border-burgundy mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-filter me-2 text-burgundy"></i>
                        @if(request('query') && request('cuisine'))
                            Filtros ativos: "{{ request('query') }}" e cozinha "{{ request('cuisine') }}"
                        @elseif(request('query'))
                            Buscando por: "{{ request('query') }}"
                        @elseif(request('cuisine'))
                            Filtrando por cozinha: "{{ request('cuisine') }}"
                        @endif
                    </div>
                    <a href="{{ route('restaurants.index') }}" class="btn btn-sm btn-outline-burgundy">
                        <i class="fas fa-times me-1"></i> Limpar
                    </a>
                </div>
            </div>
            @endif

            @if($restaurants->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($restaurants as $restaurant)
                <div class="col">
                    <div class="card restaurant-card h-100 border-0 shadow-sm">
                        <div class="position-relative">
                            @if(!empty($restaurant->photos) && count($restaurant->photos) > 0)
                            <img src="{{ $restaurant->photos[0]['url'] }}" class="card-img-top" 
                                 alt="{{ $restaurant->name }}" style="height: 200px; object-fit: cover;">
                            @else
                            <div class="card-img-top bg-dark-gray d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-utensils fa-4x text-medium-gray"></i>
                            </div>
                            @endif
                            
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-burgundy px-3 py-2">
                                    {{ number_format($restaurant->average_rating, 1) }}
                                    <i class="fas fa-star ms-1"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-white mb-2">{{ $restaurant->name }}</h5>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $restaurant->average_rating ? 'text-warning' : 'text-medium-gray' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">({{ $restaurant->total_reviews }} avaliações)</small>
                                </div>
                                <p class="card-text text-light-gray small mb-3">{{ Str::limit($restaurant->description, 80) }}</p>
                            </div>

                            <div class="mb-3">
                                @foreach(array_slice($restaurant->cuisine_types, 0, 2) as $type)
                                    <span class="badge bg-medium-gray text-light-gray me-1 mb-1">{{ $type }}</span>
                                @endforeach
                                @if(count($restaurant->cuisine_types) > 2)
                                    <span class="badge bg-dark-gray text-light-gray">+{{ count($restaurant->cuisine_types) - 2 }}</span>
                                @endif
                            </div>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1 text-burgundy"></i> 
                                        {{ Str::limit($restaurant->address, 25) }}
                                    </small>
                                    <a href="{{ route('restaurants.show', $restaurant->id) }}" 
                                       class="btn btn-sm btn-outline-burgundy">
                                        <i class="fas fa-eye me-1"></i> Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-utensils fa-5x text-medium-gray mb-4"></i>
                    <h3 class="text-light-gray mb-3">Nenhum restaurante encontrado</h3>
                    <p class="text-muted mb-4">
                        @if(request()->has('query') || request()->has('cuisine'))
                            Tente ajustar os filtros de busca.
                        @else
                            Seja o primeiro a cadastrar um restaurante!
                        @endif
                    </p>
                    <a href="{{ route('restaurants.create') }}" class="btn btn-burgundy btn-lg">
                        <i class="fas fa-plus me-2"></i> Cadastrar Primeiro Restaurante
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.restaurant-card {
    transition: all 0.3s ease;
    background: var(--dark-gray);
    border: 1px solid var(--medium-gray);
}

.restaurant-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(128, 0, 32, 0.15) !important;
    border-color: var(--burgundy);
}

.card-img-top {
    border-radius: 8px 8px 0 0;
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

.empty-state {
    max-width: 500px;
    margin: 0 auto;
    padding: 3rem;
    background: var(--dark-gray);
    border-radius: 12px;
    border: 2px dashed var(--medium-gray);
}
</style>
@endpush
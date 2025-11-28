@extends('layouts.app')

@section('title', 'Lista de Restaurantes')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-utensils"></i> 
                    Todos os Restaurantes
                </h1>
                <div>
                    <a href="{{ route('restaurants.map') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-map-marked-alt"></i> Ver Mapa
                    </a>
                    <a href="{{ route('restaurants.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Cadastrar Restaurante
                    </a>
                </div>
            </div>

            <!-- Filtros e Busca -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('restaurants.search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="query" class="form-control" 
                                       placeholder="Buscar por nome, descrição ou endereço..." 
                                       value="{{ request('query') }}">
                            </div>
                            <div class="col-md-4">
                                <select name="cuisine" class="form-select">
                                    <option value="">Todos os tipos de cozinha</option>
                                    @foreach(['Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa', 'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar'] as $cuisine)
                                        <option value="{{ $cuisine }}" {{ request('cuisine') == $cuisine ? 'selected' : '' }}>
                                            {{ $cuisine }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            

            <!-- Informações de Resultados -->
            @if(request()->has('query') || request()->has('cuisine'))
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                @if(request('query') && request('cuisine'))
                    Mostrando resultados para "{{ request('query') }}" e cozinha "{{ request('cuisine') }}"
                @elseif(request('query'))
                    Mostrando resultados para "{{ request('query') }}"
                @elseif(request('cuisine'))
                    Mostrando resultados para cozinha "{{ request('cuisine') }}"
                @endif
                <a href="{{ route('restaurants.index') }}" class="float-end">
                    <i class="fas fa-times"></i> Limpar filtros
                </a>
            </div>
            @endif

            <!-- Lista de Restaurantes -->
            <div class="row">
                @forelse($restaurants as $restaurant)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card h-100">
                        @if(!empty($restaurant->photos) && count($restaurant->photos) > 0)
                        <img src="{{ $restaurant->photos[0]['url'] }}" class="card-img-top" 
                             alt="{{ $restaurant->name }}" style="height: 200px; object-fit: cover;">
                        @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-utensils fa-3x text-muted"></i>
                        </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $restaurant->name }}</h5>
                            
                            <div class="mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $restaurant->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted ms-1">
                                    {{ number_format($restaurant->average_rating, 1) }} ({{ $restaurant->total_reviews }} avaliações)
                                </small>
                            </div>

                            <p class="card-text flex-grow-1">{{ Str::limit($restaurant->description, 100) }}</p>

                            <div class="mb-2">
                                @foreach(array_slice($restaurant->cuisine_types, 0, 3) as $type)
                                    <span class="badge bg-secondary me-1 mb-1">{{ $type }}</span>
                                @endforeach
                                @if(count($restaurant->cuisine_types) > 3)
                                    <span class="badge bg-light text-dark">+{{ count($restaurant->cuisine_types) - 3 }}</span>
                                @endif
                            </div>

                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    {{ Str::limit($restaurant->address, 50) }}
                                </small>
                            </p>

                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('restaurants.show', $restaurant->id) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-utensils fa-4x text-muted mb-3"></i>
                        <h3 class="text-muted">Nenhum restaurante encontrado</h3>
                        <p class="text-muted">
                            @if(request()->has('query') || request()->has('cuisine'))
                                Tente ajustar os filtros de busca.
                            @else
                                Seja o primeiro a cadastrar um restaurante!
                            @endif
                        </p>
                        <a href="{{ route('restaurants.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i> Cadastrar Primeiro Restaurante
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação (REMOVER POR ENQUANTO) -->
            {{-- 
            @if($restaurants->hasPages())
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Page navigation">
                        {{ $restaurants->links() }}
                    </nav>
                </div>
            </div>
            @endif 
            --}}

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.restaurant-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.restaurant-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.card-img-top {
    border-radius: 8px 8px 0 0;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
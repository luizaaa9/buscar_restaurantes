@extends('layouts.app')

@section('title', $restaurant->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('restaurants.index') }}">Restaurantes</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($restaurant->name, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8">
            <!-- Carousel de Fotos -->
            @if(!empty($restaurant->photos))
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div id="restaurantCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($restaurant->photos as $index => $photo)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ $photo['url'] }}" class="d-block w-100" 
                                     alt="{{ $restaurant->name }} - Foto {{ $index + 1 }}" 
                                     style="height: 400px; object-fit: cover;">
                            </div>
                            @endforeach
                        </div>
                        @if(count($restaurant->photos) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#restaurantCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#restaurantCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Informações do Restaurante -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Sobre o Restaurante</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-3">
                                <h2 class="h4 mb-0 me-3">{{ $restaurant->name }}</h2>
                                <div class="rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $restaurant->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2 fw-bold">{{ $restaurant->average_rating }}</span>
                                    <small class="text-muted">({{ $restaurant->total_reviews }} avaliações)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6><i class="fas fa-utensils text-primary me-2"></i>Tipos de Cozinha</h6>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach($restaurant->cuisine_types as $type)
                                        <span class="badge bg-primary">{{ $type }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6><i class="fas fa-map-marker-alt text-danger me-2"></i>Endereço</h6>
                                <p class="mb-0">{{ $restaurant->address }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6><i class="fas fa-info-circle text-info me-2"></i>Descrição</h6>
                                <p class="mb-0">{{ $restaurant->description }}</p>
                            </div>

                            <div class="mb-3">
                                <h6><i class="fas fa-chart-bar text-success me-2"></i>Estatísticas</h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h5 text-primary mb-1">{{ $restaurant->average_rating }}/5</div>
                                        <small class="text-muted">Avaliação Média</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="h5 text-primary mb-1">{{ $restaurant->total_reviews }}</div>
                                        <small class="text-muted">Total de Avaliações</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapa de Localização -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Localização</h5>
                </div>
                <div class="card-body p-0">
                    <div id="locationMap" style="height: 300px; border-radius: 0 0 8px 8px;"></div>
                </div>
            </div>

            <!-- Avaliações -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Avaliações e Comentários
                        <span class="badge bg-primary ms-2">{{ count($reviewsData) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($reviewsData) > 0)
                        <div class="reviews-list">
                            @foreach($reviewsData as $reviewId => $review)
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="d-block">{{ $review['user_name'] }}</strong>
                                        <div class="rating-small">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <small class="text-muted ms-1">{{ $review['rating'] }}/5</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            @if(isset($review['created_at']))
                                                {{ \Carbon\Carbon::parse($review['created_at'])->format('d/m/Y H:i') }}
                                            @else
                                                Data não disponível
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted">{{ $review['comment'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma avaliação ainda</h5>
                            <p class="text-muted">Seja o primeiro a avaliar este restaurante!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ações Rápidas -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Ações</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('restaurants.map') }}" class="btn btn-outline-primary">
                            <i class="fas fa-map-marked-alt me-2"></i>Ver no Mapa
                        </a>
                        <a href="{{ route('restaurants.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulário de Avaliação -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Deixe sua Avaliação</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" id="reviewForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="user_name" class="form-label">Seu Nome *</label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                   id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sua Avaliação *</label>
                            <div class="rating-input mb-2">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" 
                                       class="d-none" {{ old('rating') == $i ? 'checked' : '' }} required>
                                <label for="star{{ $i }}" class="star-label">
                                    <i class="fas fa-star"></i>
                                </label>
                                @endfor
                            </div>
                            @error('rating')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Comentário *</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="4" 
                                      placeholder="Conte sua experiência no restaurante..." 
                                      required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mínimo 10 caracteres</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i> Enviar Avaliação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    function initMap() {
        const map = L.map('locationMap').setView([{{ $restaurant->latitude }}, {{ $restaurant->longitude }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const restaurantIcon = L.divIcon({
            html: '<i class="fas fa-map-marker-alt fa-2x text-danger"></i>',
            iconSize: [30, 30],
            className: 'restaurant-marker'
        });

        L.marker([{{ $restaurant->
@extends('layouts.app')

@section('title', $restaurant->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <nav class="mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('restaurants.index') }}" class="btn btn-sm btn-outline-burgundy me-3">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('restaurants.index') }}" class="text-decoration-none text-light-gray">
                            <i class="fas fa-utensils me-1"></i>Restaurantes
                        </a></li>
                        <li class="breadcrumb-item active text-burgundy">{{ Str::limit($restaurant->name, 30) }}</li>
                    </ol>
                </div>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            @if(!empty($restaurant->photos) && count($restaurant->photos) > 0)
            <div class="card border-0 shadow-lg mb-4">
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
                            <span class="carousel-control-prev-icon bg-burgundy rounded-circle p-3"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#restaurantCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-burgundy rounded-circle p-3"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h1 class="h2 fw-bold text-white mb-2">{{ $restaurant->name }}</h1>
                            <div class="d-flex align-items-center">
                                <div class="rating-display me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $restaurant->average_rating ? 'text-warning' : 'text-medium-gray' }} fs-5"></i>
                                    @endfor
                                </div>
                                <span class="fw-bold text-burgundy fs-4">{{ $restaurant->average_rating }}</span>
                                <small class="text-muted ms-2 fs-6">({{ $restaurant->total_reviews }} avaliações)</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-utensils me-2 text-burgundy"></i>Tipos de Cozinha
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($restaurant->cuisine_types as $type)
                                        <span class="badge bg-burgundy bg-opacity-10 text-burgundy border border-burgundy border-opacity-25 py-2 px-3">
                                            {{ $type }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="info-section mt-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt me-2 text-burgundy"></i>Endereço
                                </h6>
                                <p class="mb-0 text-white fs-6">{{ $restaurant->address }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2 text-burgundy"></i>Descrição
                                </h6>
                                <p class="mb-0 text-light-gray lh-base">{{ $restaurant->description }}</p>
                            </div>

                            <div class="info-section mt-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-chart-bar me-2 text-burgundy"></i>Estatísticas
                                </h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end border-dark">
                                            <div class="h3 text-burgundy mb-1">{{ $restaurant->average_rating }}/5</div>
                                            <small class="text-muted">Avaliação Média</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h3 text-burgundy mb-1">{{ $restaurant->total_reviews }}</div>
                                        <small class="text-muted">Total de Avaliações</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-map-marked-alt me-2 text-burgundy"></i>Localização
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="locationMap" style="height: 300px; border-radius: 0 0 8px 8px;"></div>
                </div>
            </div>

            <div class="card border-0 shadow-lg">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-comments me-2 text-burgundy"></i>
                            Avaliações e Comentários
                        </h5>
                        <span class="badge bg-burgundy rounded-pill px-3 py-2">{{ count($reviewsData) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($reviewsData) > 0)
                        <div class="reviews-list">
                            @foreach($reviewsData as $reviewId => $review)
                            <div class="review-item border-bottom border-dark pb-4 mb-4">
                                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                                    <div>
                                        <strong class="d-block text-white fs-5">{{ $review['user_name'] }}</strong>
                                        <div class="rating-small mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review['rating'] ? 'text-warning' : 'text-medium-gray' }}"></i>
                                            @endfor
                                            <small class="text-muted ms-2">{{ $review['rating'] }}/5</small>
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
                                <p class="mb-0 text-light-gray lh-base">{{ $review['comment'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-4x text-medium-gray mb-4"></i>
                            <h5 class="text-light-gray mb-3">Nenhuma avaliação ainda</h5>
                            <p class="text-muted mb-4">Seja o primeiro a avaliar este restaurante!</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('restaurants.map') }}" class="btn btn-outline-burgundy">
                                    <i class="fas fa-map-marked-alt me-1"></i> Ver no Mapa
                                </a>
                                <a href="{{ route('restaurants.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar para Lista
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg sticky-top" style="top: 20px;">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <h6 class="mb-0 text-white">
                        <i class="fas fa-edit me-2 text-burgundy"></i>Deixe sua Avaliação
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" id="reviewForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="user_name" class="form-label fw-semibold text-light-gray">Seu Nome</label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                   id="user_name" name="user_name" value="{{ old('user_name') }}" 
                                   placeholder="Digite seu nome" required>
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-light-gray mb-3">Sua Avaliação</label>
                            <div class="rating-input mb-3">
                                @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" 
                                       class="d-none" {{ old('rating') == $i ? 'checked' : '' }} required>
                                <label for="star{{ $i }}" class="star-label">
                                    <i class="fas fa-star fa-2x"></i>
                                </label>
                                @endfor
                            </div>
                            @error('rating')
                                <div class="text-burgundy small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label fw-semibold text-light-gray">Comentário</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="5" 
                                      placeholder="Conte sua experiência no restaurante..." 
                                      required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mínimo 10 caracteres</small>
                        </div>

                        <button type="submit" class="btn btn-burgundy w-100 py-3 fw-semibold">
                            <i class="fas fa-paper-plane me-2"></i> Enviar Avaliação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.breadcrumb {
    background: transparent;
    padding: 0;
}

.breadcrumb-item a {
    color: var(--light-gray);
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: var(--burgundy);
}

.card {
    background: var(--dark-gray);
    border: 1px solid var(--medium-gray);
}

.rating-display .fa-star {
    transition: color 0.2s ease;
}

.rating-small .fa-star {
    font-size: 0.9rem;
}

.star-label {
    cursor: pointer;
    color: var(--medium-gray);
    margin-right: 0.5rem;
    transition: all 0.2s ease;
}

.star-label:hover,
.rating-input input:checked ~ .star-label {
    color: var(--burgundy);
    transform: scale(1.1);
}

.review-item {
    transition: background-color 0.2s ease;
    padding: 1.5rem;
    border-radius: 8px;
}

.review-item:hover {
    background-color: rgba(128, 0, 32, 0.05);
}

.sticky-top {
    z-index: 1020;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-size: 1.5rem;
}

@media (max-width: 768px) {
    .sticky-top {
        position: static !important;
    }
}
</style>
@endpush

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
            html: '<div class="custom-marker"><i class="fas fa-map-marker-alt fa-3x text-burgundy"></i></div>',
            iconSize: [40, 40],
            className: 'restaurant-marker'
        });

        L.marker([{{ $restaurant->latitude }}, {{ $restaurant->longitude }}], {icon: restaurantIcon})
            .addTo(map)
            .bindPopup(`
                <div class="text-center">
                    <h6 class="mb-1 fw-bold">{{ $restaurant->name }}</h6>
                    <small class="text-muted">{{ $restaurant->address }}</small>
                </div>
            `)
            .openPopup();
    }

    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        
        const starLabels = document.querySelectorAll('.star-label');
        starLabels.forEach(label => {
            label.addEventListener('click', function() {
                const rating = this.htmlFor.replace('star', '');
                
                starLabels.forEach(star => {
                    star.querySelector('i').style.color = 'var(--medium-gray)';
                });
                
                starLabels.forEach(star => {
                    const starNum = star.htmlFor.replace('star', '');
                    if (starNum >= rating) {
                        star.querySelector('i').style.color = 'var(--burgundy)';
                    }
                });
            });
        });
    });
</script>
@endpush
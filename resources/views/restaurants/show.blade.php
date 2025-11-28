@extends('layouts.app')

@section('title', $restaurant->name)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('restaurants.index') }}" class="text-decoration-none">
                <i class="fas fa-utensils me-1"></i>Restaurantes
            </a></li>
            <li class="breadcrumb-item active text-muted">{{ Str::limit($restaurant->name, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8">
            <!-- Carousel de Fotos -->
            @if(!empty($restaurant->photos) && count($restaurant->photos) > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div id="restaurantCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-top">
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
                            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#restaurantCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-dark rounded-circle p-2"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Informações do Restaurante -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3 mb-2 text-dark">{{ $restaurant->name }}</h1>
                            <div class="d-flex align-items-center">
                                <div class="rating-display me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $restaurant->average_rating ? 'text-warning' : 'text-light' }}"></i>
                                    @endfor
                                </div>
                                <span class="fw-bold text-primary fs-5">{{ $restaurant->average_rating }}</span>
                                <small class="text-muted ms-1">({{ $restaurant->total_reviews }} avaliações)</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body pt-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-utensils me-2 text-primary"></i>Tipos de Cozinha
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($restaurant->cuisine_types as $type)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3">
                                            {{ $type }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="info-section mt-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt me-2 text-danger"></i>Endereço
                                </h6>
                                <p class="mb-0 text-dark">{{ $restaurant->address }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2 text-info"></i>Descrição
                                </h6>
                                <p class="mb-0 text-dark lh-base">{{ $restaurant->description }}</p>
                            </div>

                            <div class="info-section mt-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-chart-bar me-2 text-success"></i>Estatísticas
                                </h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <div class="h4 text-primary mb-1">{{ $restaurant->average_rating }}/5</div>
                                            <small class="text-muted">Avaliação Média</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h4 text-primary mb-1">{{ $restaurant->total_reviews }}</div>
                                        <small class="text-muted">Total de Avaliações</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapa de Localização -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-map-marked-alt me-2 text-primary"></i>Localização
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="locationMap" style="height: 300px; border-radius: 0 0 8px 8px;"></div>
                </div>
            </div>

            <!-- Avaliações -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-comments me-2 text-primary"></i>
                            Avaliações e Comentários
                        </h5>
                        <span class="badge bg-primary rounded-pill">{{ count($reviewsData) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($reviewsData) > 0)
                        <div class="reviews-list">
                            @foreach($reviewsData as $reviewId => $review)
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="d-block text-dark">{{ $review['user_name'] }}</strong>
                                        <div class="rating-small mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review['rating'] ? 'text-warning' : 'text-light' }}"></i>
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
                                <p class="mb-0 text-dark lh-base mt-2">{{ $review['comment'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-light mb-3"></i>
                            <h5 class="text-muted">Nenhuma avaliação ainda</h5>
                            <p class="text-muted mb-0">Seja o primeiro a avaliar este restaurante!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ações Rápidas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-bolt me-2 text-warning"></i>Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('restaurants.map') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-map-marked-alt me-2"></i>Ver no Mapa
                        </a>
                        <a href="{{ route('restaurants.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulário de Avaliação -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-edit me-2 text-success"></i>Deixe sua Avaliação
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" id="reviewForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="user_name" class="form-label small fw-semibold text-muted">Seu Nome *</label>
                            <input type="text" class="form-control form-control-sm @error('user_name') is-invalid @enderror" 
                                   id="user_name" name="user_name" value="{{ old('user_name') }}" 
                                   placeholder="Digite seu nome" required>
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Sua Avaliação *</label>
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
                            <label for="comment" class="form-label small fw-semibold text-muted">Comentário *</label>
                            <textarea class="form-control form-control-sm @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="4" 
                                      placeholder="Conte sua experiência no restaurante..." 
                                      required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mínimo 10 caracteres</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-sm">
                            <i class="fas fa-paper-plane me-1"></i> Enviar Avaliação
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
    color: #6c757d;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #007bff;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.info-section {
    padding: 1rem 0;
}

.info-section:not(:last-child) {
    border-bottom: 1px solid #f8f9fa;
}

.rating-display .fa-star {
    font-size: 1.1rem;
}

.rating-small .fa-star {
    font-size: 0.9rem;
}

.star-label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #dee2e6;
    margin-right: 0.25rem;
    transition: color 0.2s ease;
}

.star-label:hover,
.rating-input input:checked ~ .star-label {
    color: #ffc107;
}

.review-item {
    transition: background-color 0.2s ease;
    padding: 1rem;
    border-radius: 8px;
}

.review-item:hover {
    background-color: #f8f9fa;
}

.review-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
}

.badge {
    font-weight: 500;
}

.form-control {
    border: 1px solid #e9ecef;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

.btn {
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-size: 1rem;
}

.sticky-top {
    z-index: 1020;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .info-section {
        padding: 0.75rem 0;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    // Mapa da localização do restaurante
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

        L.marker([{{ $restaurant->latitude }}, {{ $restaurant->longitude }}], {icon: restaurantIcon})
            .addTo(map)
            .bindPopup(`
                <div class="text-center">
                    <h6 class="mb-1">{{ $restaurant->name }}</h6>
                    <small class="text-muted">{{ $restaurant->address }}</small>
                </div>
            `)
            .openPopup();
    }

    // Inicializar mapa quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        
        // Interatividade das estrelas no formulário de avaliação
        const starLabels = document.querySelectorAll('.star-label');
        starLabels.forEach(label => {
            label.addEventListener('click', function() {
                const rating = this.htmlFor.replace('star', '');
                
                // Reset all stars
                starLabels.forEach(star => {
                    star.querySelector('i').style.color = '#dee2e6';
                });
                
                // Color stars up to selected rating
                starLabels.forEach(star => {
                    const starNum = star.htmlFor.replace('star', '');
                    if (starNum >= rating) {
                        star.querySelector('i').style.color = '#ffc107';
                    }
                });
            });
        });
    });
</script>
@endpush
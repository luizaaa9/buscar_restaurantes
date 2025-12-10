@extends('layouts.app')

@section('title', $restaurant->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('restaurants.index') }}" class="text-decoration-none text-light-gray">
                            <i class="fas fa-utensils me-1"></i> Restaurantes
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-burgundy" aria-current="page">
                        {{ Str::limit($restaurant->name, 30) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Restaurant Details -->
        <div class="col-lg-8">
            <!-- Photo Gallery -->
            @if(!empty($restaurant->photos) && count($restaurant->photos) > 0)
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-body p-0">
                    <div id="restaurantCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($restaurant->photos as $index => $photo)
                                @php
                                    $photoUrl = is_array($photo) ? $photo['url'] : (is_string($photo) ? $photo : '');
                                @endphp
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $photoUrl }}" 
                                         class="d-block w-100 carousel-image" 
                                         alt="{{ $restaurant->name }} - Foto {{ $index + 1 }}"
                                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';">
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

            <!-- Restaurant Info Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h1 class="h2 fw-bold text-white mb-2">{{ $restaurant->name }}</h1>
                            <div class="d-flex align-items-center">
                                <div class="rating-display me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($restaurant->average_rating))
                                            <i class="fas fa-star text-warning fs-4"></i>
                                        @elseif($i - 0.5 <= $restaurant->average_rating)
                                            <i class="fas fa-star-half-alt text-warning fs-4"></i>
                                        @else
                                            <i class="far fa-star text-medium-gray fs-4"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="fw-bold text-burgundy fs-3">{{ number_format($restaurant->average_rating, 1) }}</span>
                                <small class="text-muted ms-2 fs-6">({{ $restaurant->total_reviews }} avaliações)</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('restaurants.map') }}" class="btn btn-outline-burgundy">
                                <i class="fas fa-map-marked-alt me-1"></i> Ver no Mapa
                            </a>
                            <a href="{{ route('restaurants.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Cuisine Types -->
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-utensils me-2 text-burgundy"></i>Tipos de Cozinha
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @if(!empty($restaurant->cuisine_types))
                                        @foreach($restaurant->cuisine_types as $type)
                                            <span class="badge bg-burgundy bg-opacity-10 text-burgundy border border-burgundy border-opacity-25 py-2 px-3">
                                                {{ $type }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-light-gray">Não especificado</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="info-section mt-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt me-2 text-burgundy"></i>Endereço
                                </h6>
                                <p class="mb-0 text-white fs-6">
                                    <i class="fas fa-location-dot me-2 text-burgundy"></i>
                                    {{ $restaurant->address }}
                                </p>
                                @if($restaurant->latitude && $restaurant->longitude)
                                <small class="text-muted">
                                    Coordenadas: {{ number_format($restaurant->latitude, 6) }}, {{ number_format($restaurant->longitude, 6) }}
                                </small>
                                @endif
                            </div>
                        </div>

                        <!-- Description & Stats -->
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2 text-burgundy"></i>Descrição
                                </h6>
                                <p class="mb-0 text-light-gray lh-base">{{ $restaurant->description }}</p>
                            </div>

                            <!-- Statistics -->
                            <div class="info-section mt-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="fas fa-chart-bar me-2 text-burgundy"></i>Estatísticas
                                </h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="border-end border-dark py-2">
                                            <div class="h3 text-burgundy mb-1">{{ number_format($restaurant->average_rating, 1) }}</div>
                                            <small class="text-muted">Avaliação</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-end border-dark py-2">
                                            <div class="h3 text-burgundy mb-1">{{ $restaurant->total_reviews }}</div>
                                            <small class="text-muted">Avaliações</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="py-2">
                                            <div class="h3 text-burgundy mb-1">
                                                @if(!empty($restaurant->photos)){{ count($restaurant->photos) }}@else 0 @endif
                                            </div>
                                            <small class="text-muted">Fotos</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-comments me-2 text-burgundy"></i>
                            Avaliações e Comentários
                            <span class="badge bg-burgundy rounded-pill ms-2">{{ $restaurant->reviews->count() }}</span>
                        </h5>
                        <button class="btn btn-sm btn-outline-burgundy" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            <i class="fas fa-plus me-1"></i> Nova Avaliação
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($restaurant->reviews->count() > 0)
                        <div class="reviews-list">
                            @foreach($restaurant->reviews->sortByDesc('created_at') as $review)
                            <div class="review-item border-bottom border-dark pb-4 mb-4">
                                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                                    <div>
                                        <strong class="d-block text-white fs-5">{{ $review->user_name }}</strong>
                                        <div class="rating-small mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-medium-gray"></i>
                                                @endif
                                            @endfor
                                            <small class="text-muted ms-2">{{ $review->rating }}/5</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            {{ $review->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-0 text-light-gray lh-base">{{ $review->comment }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-4x text-medium-gray mb-4"></i>
                            <h5 class="text-light-gray mb-3">Nenhuma avaliação ainda</h5>
                            <p class="text-muted mb-4">Seja o primeiro a avaliar este restaurante!</p>
                            <button class="btn btn-burgundy" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="fas fa-plus me-1"></i> Adicionar Primeira Avaliação
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Review Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg sticky-top" style="top: 20px;">
                <div class="card-header bg-dark-gray border-bottom-0 py-4">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-star me-2 text-burgundy"></i>Avaliar Restaurante
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" id="reviewForm">
                        @csrf
                        
                        <!-- User Name -->
                        <div class="mb-4">
                            <label for="user_name" class="form-label fw-semibold text-light-gray">
                                <i class="fas fa-user me-1 text-burgundy"></i>Seu Nome
                            </label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                   id="user_name" name="user_name" value="{{ old('user_name') }}" 
                                   placeholder="Digite seu nome" required>
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Star Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-light-gray mb-3">
                                <i class="fas fa-star me-1 text-burgundy"></i>Sua Avaliação
                            </label>
                            <div class="rating-input mb-3">
                                <div class="d-flex justify-content-center mb-2">
                                    @for($i = 5; $i >= 1; $i--)
                                    <div class="star-container">
                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" 
                                               class="d-none" {{ old('rating') == $i ? 'checked' : '' }} required>
                                        <label for="star{{ $i }}" class="star-label" data-value="{{ $i }}">
                                            <i class="far fa-star fa-2x"></i>
                                        </label>
                                        <span class="star-number">{{ $i }}</span>
                                    </div>
                                    @endfor
                                </div>
                                <div class="text-center">
                                    <small class="text-muted" id="ratingText">Selecione uma nota</small>
                                </div>
                            </div>
                            @error('rating')
                                <div class="text-burgundy small text-center">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-semibold text-light-gray">
                                <i class="fas fa-comment me-1 text-burgundy"></i>Comentário
                            </label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="5" 
                                      placeholder="Conte sua experiência no restaurante..." 
                                      required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Mínimo 10 caracteres</small>
                                <small class="text-muted"><span id="charCount">0</span>/1000</small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-burgundy w-100 py-3 fw-semibold" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i> Enviar Avaliação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal (for mobile) -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark-gray border border-burgundy">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title text-white">
                    <i class="fas fa-star me-2 text-burgundy"></i>Avaliar Restaurante
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" id="modalReviewForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="modal_user_name" class="form-label text-light-gray">Seu Nome</label>
                        <input type="text" class="form-control" id="modal_user_name" name="user_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light-gray">Avaliação</label>
                        <div class="rating-input-modal text-center">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="modal_star{{ $i }}" name="rating" value="{{ $i }}" class="d-none">
                            <label for="modal_star{{ $i }}" class="star-label-modal" data-value="{{ $i }}">
                                <i class="far fa-star fa-2x"></i>
                            </label>
                            @endfor
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted" id="modalRatingText">Selecione uma nota</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modal_comment" class="form-label text-light-gray">Comentário</label>
                        <textarea class="form-control" id="modal_comment" name="comment" rows="4" required></textarea>
                        <small class="text-muted">Mínimo 10 caracteres</small>
                    </div>
                    
                    <button type="submit" class="btn btn-burgundy w-100">Enviar Avaliação</button>
                </form>
            </div>
        </div>
    </div>
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

.carousel-image {
    height: 400px;
    object-fit: cover;
    border-radius: 0;
}

.breadcrumb {
    background: transparent;
    padding: 0.75rem 0;
}

.breadcrumb-item a {
    color: var(--light-gray);
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: var(--burgundy);
    text-decoration: none;
}

.rating-display .fa-star,
.rating-display .fa-star-half-alt {
    margin-right: 2px;
}

.info-section {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border-left: 4px solid var(--burgundy);
}

.star-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 0 5px;
}

.star-label {
    cursor: pointer;
    color: var(--medium-gray);
    transition: all 0.2s ease;
    margin-bottom: 5px;
}

.star-label:hover,
.star-label:hover ~ .star-label,
.rating-input input:checked ~ .star-label,
.rating-input-modal input:checked ~ .star-label-modal {
    color: #ffc107;
}

.star-label.active,
.star-label-modal.active {
    color: #ffc107 !important;
}

.star-number {
    color: var(--light-gray);
    font-size: 0.8rem;
    font-weight: bold;
}

.rating-input input:checked + .star-label {
    color: #ffc107;
}

.rating-input-modal input:checked + .star-label-modal {
    color: #ffc107;
}

.review-item {
    transition: background-color 0.2s ease;
    padding: 1.5rem;
    border-radius: 8px;
}

.review-item:hover {
    background-color: rgba(128, 0, 32, 0.05);
}

.rating-small .fa-star {
    font-size: 0.9rem;
    margin-right: 1px;
}

.sticky-top {
    z-index: 1020;
}

/* Modal Styles */
.modal-content {
    background: var(--dark-gray);
    border: 2px solid var(--burgundy);
}

.modal-header {
    border-bottom: 1px solid var(--medium-gray);
}

.star-label-modal {
    cursor: pointer;
    color: var(--medium-gray);
    margin: 0 5px;
    transition: all 0.2s ease;
}

.star-label-modal:hover {
    color: #ffc107;
    transform: scale(1.2);
}

@media (max-width: 992px) {
    .sticky-top {
        position: static !important;
        margin-top: 2rem;
    }
    
    .carousel-image {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .carousel-image {
        height: 250px;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const starLabels = document.querySelectorAll('.star-label');
    const ratingText = document.getElementById('ratingText');
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    
    const ratingMessages = {
        1: 'Péssimo - Não recomendo',
        2: 'Ruim - Precisa melhorar',
        3: 'Regular - Satisfatório',
        4: 'Bom - Recomendo',
        5: 'Excelente - Altamente recomendado'
    };
    
    function updateStars(selectedValue) {
        starLabels.forEach(label => {
            const value = parseInt(label.dataset.value);
            const icon = label.querySelector('i');
            
            if (value <= selectedValue) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                label.classList.add('active');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                label.classList.remove('active');
            }
        });
        
        if (ratingText && ratingMessages[selectedValue]) {
            ratingText.textContent = ratingMessages[selectedValue];
            ratingText.className = 'text-warning fw-semibold';
        }
    }
    
    const selectedRating = document.querySelector('input[name="rating"]:checked');
    if (selectedRating) {
        updateStars(parseInt(selectedRating.value));
    }
    
    starLabels.forEach(label => {
        label.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            updateStars(value);
            
            const input = document.getElementById(`star${value}`);
            if (input) {
                input.checked = true;
            }
        });
        
        label.addEventListener('mouseenter', function() {
            const value = parseInt(this.dataset.value);
            const icon = this.querySelector('i');
            icon.style.transform = 'scale(1.2)';
            
            starLabels.forEach(otherLabel => {
                const otherValue = parseInt(otherLabel.dataset.value);
                const otherIcon = otherLabel.querySelector('i');
                
                if (otherValue <= value) {
                    otherIcon.style.color = '#ffc107';
                }
            });
        });
        
        label.addEventListener('mouseleave', function() {
            const value = parseInt(this.dataset.value);
            const selectedValue = document.querySelector('input[name="rating"]:checked')?.value || 0;
            const icon = this.querySelector('i');
            icon.style.transform = 'scale(1)';
            
            updateStars(parseInt(selectedValue));
        });
    });
    
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('charCount');
    
    if (commentTextarea && charCount) {
        commentTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length < 10) {
                charCount.className = 'text-danger';
            } else if (length > 900) {
                charCount.className = 'text-warning';
            } else {
                charCount.className = 'text-success';
            }
        });
        
        charCount.textContent = commentTextarea.value.length;
    }
    
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            const rating = document.querySelector('input[name="rating"]:checked');
            const comment = document.getElementById('comment');
            const userName = document.getElementById('user_name');
            const submitBtn = document.getElementById('submitBtn');
            
            let isValid = true;
            let errorMessage = '';
            
            if (!userName.value.trim()) {
                isValid = false;
                errorMessage = 'Por favor, digite seu nome.';
                userName.focus();
            } else if (!rating) {
                isValid = false;
                errorMessage = 'Por favor, selecione uma avaliação.';
            } else if (!comment.value.trim() || comment.value.trim().length < 10) {
                isValid = false;
                errorMessage = 'O comentário deve ter pelo menos 10 caracteres.';
                comment.focus();
            }
            
            if (!isValid) {
                e.preventDefault();
                showAlert(errorMessage, 'danger');
                return false;
            }
            
            
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
                submitBtn.disabled = true;
            }
        });
    }
    
    const modalStarLabels = document.querySelectorAll('.star-label-modal');
    const modalRatingText = document.getElementById('modalRatingText');
    const modalRatingInputs = document.querySelectorAll('#modalReviewForm input[name="rating"]');
    
    modalStarLabels.forEach(label => {
        label.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            
            
            modalStarLabels.forEach(modalLabel => {
                const modalValue = parseInt(modalLabel.dataset.value);
                const modalIcon = modalLabel.querySelector('i');
                
                if (modalValue <= value) {
                    modalIcon.classList.remove('far');
                    modalIcon.classList.add('fas');
                    modalLabel.classList.add('active');
                } else {
                    modalIcon.classList.remove('fas');
                    modalIcon.classList.add('far');
                    modalLabel.classList.remove('active');
                }
            });
            
            if (modalRatingText && ratingMessages[value]) {
                modalRatingText.textContent = ratingMessages[value];
                modalRatingText.className = 'text-warning fw-semibold';
            }
            
            const modalInput = document.getElementById(`modal_star${value}`);
            if (modalInput) {
                modalInput.checked = true;
            }
        });
    });
    
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                <div>${message}</div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    @if(session('success') || session('error'))
        showAlert('{{ session('success') ?? session('error') }}', '{{ session('success') ? 'success' : 'danger' }}');
    @endif
});
</script>
@endpush
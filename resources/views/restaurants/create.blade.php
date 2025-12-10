@extends('layouts.app')

@section('title', 'Cadastrar Novo Restaurante')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-burgundy text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white text-burgundy p-3 me-3">
                            <i class="fas fa-store fa-lg"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-1">Cadastrar Novo Restaurante</h1>
                            <p class="mb-0 opacity-75">Preencha os detalhes abaixo para adicionar um novo restaurante</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-5">
                    <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data" id="restaurantForm">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="fas fa-signature me-2 text-burgundy"></i>
                                        Nome do Restaurante
                                    </label>
                                    <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Digite o nome do restaurante" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="fas fa-align-left me-2 text-burgundy"></i>
                                        Descrição
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="6" 
                                              placeholder="Descreva o ambiente, especialidades da casa, atmosfera..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted">Conte o que torna este restaurante especial</small>
                                        <small class="text-muted"><span id="charCount">0</span>/500 caracteres</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="address" class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-2 text-burgundy"></i>
                                        Endereço Completo
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                               id="address" name="address" value="{{ old('address') }}" 
                                               placeholder="Ex: Rua das Flores, 123 - Centro, São Paulo - SP" required>
                                        <button type="button" class="btn btn-burgundy" id="searchAddress">
                                            <i class="fas fa-search me-1"></i> Buscar
                                        </button>
                                    </div>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Digite o endereço completo e clique em buscar para encontrar no mapa</small>
                                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                                </div>

                                <div class="alert alert-dark border-burgundy">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-crosshairs me-3 text-burgundy fa-lg"></i>
                                        <div>
                                            <small class="fw-semibold">Coordenadas atuais:</small>
                                            <div id="coordinatesDisplay" class="text-muted">
                                                @if(old('latitude') && old('longitude'))
                                                    <span class="text-burgundy">{{ old('latitude') }}, {{ old('longitude') }}</span>
                                                @else
                                                    <span class="text-warning">Clique no mapa para definir a localização</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map-marked-alt me-2 text-burgundy"></i>
                                        Localização no Mapa
                                    </label>
                                    <div id="locationMap" style="height: 250px; border-radius: 8px; border: 2px solid var(--burgundy);"></div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-mouse-pointer me-1"></i>
                                            Clique no mapa para definir a localização exata
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-utensils me-2 text-burgundy"></i>
                                        Tipos de Cozinha
                                    </label>
                                    <div class="cuisine-grid">
                                        @php
                                            $cuisineTypes = [
                                                'Brasileira' => '',
                                                'Italiana' => '', 
                                                'Japonesa' => '',
                                                'Mexicana' => '',
                                                'Chinesa' => '',
                                                'Árabe' => '',
                                                'Francesa' => '',
                                                'Vegetariana' => '',
                                                'Vegana' => '',
                                                'Frutos do Mar' => '',
                                                'Café' => '',
                                                'Sobremesas' => '',
                                                'Fast Food' => '',
                                                'Pizza' => '',
                                                'Churrascaria' => '',
                                                'Portuguesa' => '',
                                                'Argentina' => '',
                                                'Coreana' => '',
                                                'Tailandesa' => '',
                                                'Indiana' => ''
                                            ];
                                            $oldCuisines = old('cuisine_types', []);
                                        @endphp
                                        
                                        <div class="row g-2">
                                            @foreach($cuisineTypes as $type => $emoji)
                                            <div class="col-6 col-sm-4">
                                                <div class="cuisine-option">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="cuisine_types[]" value="{{ $type }}" 
                                                           id="cuisine_{{ Str::slug($type) }}"
                                                           {{ in_array($type, $oldCuisines) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="cuisine_{{ Str::slug($type) }}">
                                                        <span class="cuisine-emoji me-2">{{ $emoji }}</span>
                                                        {{ $type }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @error('cuisine_types')
                                        <div class="text-burgundy small mt-2">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-2 d-block">
                                        Selecione todos os tipos de cozinha servidos
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label for="photos" class="form-label fw-semibold">
                                        <i class="fas fa-camera me-2 text-burgundy"></i>
                                        Fotos do Restaurante
                                    </label>
                                    <div class="file-upload-area">
                                        <div class="text-center py-5">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="mb-2">Arraste e solte as fotos aqui ou</p>
                                            <input type="file" class="form-control d-none" 
                                                   id="photos" name="photos[]" multiple accept="image/*">
                                            <button type="button" class="btn btn-outline-burgundy" onclick="document.getElementById('photos').click()">
                                                <i class="fas fa-folder-open me-1"></i> Selecionar Fotos
                                            </button>
                                            <small class="text-muted d-block mt-3">
                                                Até 10 fotos • JPEG, PNG, JPG, GIF, WEBP • Máx. 5MB cada
                                            </small>
                                        </div>
                                    </div>
                                    @error('photos')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                    <div id="photoPreview" class="row g-2 mt-3"></div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="d-flex justify-content-between align-items-center pt-4 border-top border-dark">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1 text-burgundy"></i>
                                    Seus dados estão seguros conosco
                                </small>
                            </div>
                            <div class="d-flex gap-3">
                                <a href="{{ route('restaurants.map') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-burgundy px-4" id="submitBtn">
                                    <i class="fas fa-paper-plane me-1"></i> Cadastrar Restaurante
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.cuisine-grid {
    background: var(--dark-gray);
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid var(--medium-gray);
}

.cuisine-option {
    margin-bottom: 0.5rem;
}

.cuisine-option .form-check-input {
    margin-right: 0.5rem;
    background-color: var(--medium-gray);
    border-color: #555;
}

.cuisine-option .form-check-input:checked {
    background-color: var(--burgundy);
    border-color: var(--burgundy);
}

.cuisine-option .form-check-label {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
    color: var(--light-gray);
}

.cuisine-option .form-check-label:hover {
    background: var(--medium-gray);
}

.cuisine-option .form-check-input:checked + .form-check-label {
    background: rgba(128, 0, 32, 0.1);
    color: var(--burgundy);
}

.cuisine-emoji {
    font-size: 1.2em;
}

.file-upload-area {
    border: 2px dashed var(--medium-gray);
    border-radius: 8px;
    transition: all 0.3s ease;
    background: var(--dark-gray);
}

.file-upload-area:hover {
    border-color: var(--burgundy);
    background: rgba(128, 0, 32, 0.05);
}

.photo-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: var(--medium-gray);
}

.photo-preview-item img {
    transition: transform 0.3s ease;
    width: 100%;
    height: 100px;
    object-fit: cover;
}

.photo-preview-item:hover img {
    transform: scale(1.05);
}

.remove-photo-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.remove-photo-btn:hover {
    background: #dc3545;
    transform: scale(1.1);
}

.btn-burgundy {
    background-color: var(--burgundy);
    border-color: var(--burgundy);
    color: white;
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
}

.btn-outline-burgundy:hover {
    background-color: var(--burgundy);
    border-color: var(--burgundy);
    color: white;
}

.border-burgundy {
    border-color: var(--burgundy) !important;
}

.text-burgundy {
    color: var(--burgundy) !important;
}

.bg-burgundy {
    background-color: var(--burgundy) !important;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
let map, marker;

function initMap() {
    map = L.map('locationMap').setView([-23.5505, -46.6333], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    map.on('click', function(e) {
        setMarkerLocation(e.latlng.lat, e.latlng.lng);
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            
            L.marker([userLat, userLng])
                .addTo(map)
                .bindPopup('Sua localização atual')
                .openPopup();
                
            if (!document.getElementById('latitude').value) {
                map.setView([userLat, userLng], 13);
            }
        });
    }

    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        setMarkerLocation(parseFloat(oldLat), parseFloat(oldLng));
        map.setView([oldLat, oldLng], 15);
    }
}

function setMarkerLocation(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    document.getElementById('coordinatesDisplay').innerHTML = 
        `<span class="text-burgundy fw-semibold">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>`;

    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker([lat, lng]).addTo(map)
        .bindPopup('Localização do restaurante')
        .openPopup();
}

document.getElementById('searchAddress').addEventListener('click', function() {
    const address = document.getElementById('address').value;
    
    if (!address) {
        showAlert('Por favor, digite um endereço para buscar.', 'warning');
        return;
    }

    const btn = this;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';
    btn.disabled = true;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                
                map.setView([lat, lng], 16);
                setMarkerLocation(lat, lng);
                
                document.getElementById('address').value = data[0].display_name;
                showAlert('Localização encontrada com sucesso!', 'success');
            } else {
                showAlert('Endereço não encontrado. Tente um endereço mais específico.', 'danger');
            }
        })
        .catch(error => {
            console.error('Erro ao buscar endereço:', error);
            showAlert('Erro ao buscar endereço. Tente novamente.', 'danger');
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
});

document.getElementById('photos').addEventListener('change', function(e) {
    const preview = document.getElementById('photoPreview');
    preview.innerHTML = '';
    
    const files = e.target.files;
    const maxFiles = 10;
    
    if (files.length > maxFiles) {
        showAlert(`Você pode selecionar no máximo ${maxFiles} fotos.`, 'warning');
        this.value = '';
        return;
    }

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        if (file.size > 5 * 1024 * 1024) {
            showAlert(`O arquivo "${file.name}" é muito grande. Tamanho máximo: 5MB.`, 'warning');
            this.value = '';
            preview.innerHTML = '';
            return;
        }
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-3';
                col.innerHTML = `
                    <div class="photo-preview-item">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-photo-btn" onclick="removePhotoPreview(this)">
                            <i class="fas fa-times"></i>
                        </button>
                        <small class="d-block text-center text-truncate mt-1">${file.name}</small>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    }
});

function removePhotoPreview(button) {
    button.closest('.col-4').remove();
}

document.getElementById('description').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('charCount').textContent = charCount;
    
    if (charCount > 500) {
        this.value = this.value.substring(0, 500);
        document.getElementById('charCount').textContent = 500;
        showAlert('Limite de 500 caracteres atingido.', 'warning');
    }
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

document.getElementById('restaurantForm').addEventListener('submit', function(e) {
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;
    const cuisineTypes = document.querySelectorAll('input[name="cuisine_types[]"]:checked');

    let isValid = true;
    let errorMessage = '';

    if (!latitude || !longitude) {
        isValid = false;
        errorMessage = 'Por favor, selecione a localização do restaurante no mapa.';
    } else if (cuisineTypes.length === 0) {
        isValid = false;
        errorMessage = 'Por favor, selecione pelo menos um tipo de cozinha.';
    }

    if (!isValid) {
        e.preventDefault();
        showAlert(errorMessage, 'danger');
        return false;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';
    submitBtn.disabled = true;
});

const fileUploadArea = document.querySelector('.file-upload-area');
const fileInput = document.getElementById('photos');

fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = 'var(--burgundy)';
    fileUploadArea.style.background = 'rgba(128, 0, 32, 0.1)';
});

fileUploadArea.addEventListener('dragleave', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = 'var(--medium-gray)';
    fileUploadArea.style.background = 'var(--dark-gray)';
});

fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = 'var(--medium-gray)';
    fileUploadArea.style.background = 'var(--dark-gray)';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        fileInput.dispatchEvent(new Event('change'));
    }
});

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    const description = document.getElementById('description');
    document.getElementById('charCount').textContent = description.value.length;
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Cadastrar Novo Restaurante')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
                
                <div class="card-body p-4">
                    <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data" id="restaurantForm">
                        @csrf

                        <div class="row">
                            <!-- Coluna Esquerda - Informações Básicas -->
                            <div class="col-lg-6">
                                <!-- Nome do Restaurante -->
                                <div class="mb-4">
                                    <label for="name" class="form-label fs-6 fw-semibold">
                                        Nome do Restaurante <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-store text-primary"></i>
                                        </span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" 
                                               placeholder="Ex: Sabor Brasileiro" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                </div>

                                <!-- Descrição -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fs-6 fw-semibold">
                                        Descrição <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="5" 
                                              placeholder="Descreva o ambiente, especialidades da casa, atmosfera..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">Conte o que torna este restaurante especial</small>
                                        <small class="text-muted"><span id="charCount">0</span>/500 caracteres</small>
                                    </div>
                                </div>

                                <!-- Endereço -->
                                <div class="mb-4">
                                    <label for="address" class="form-label fs-6 fw-semibold">
                                        Endereço Completo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-map-marker-alt text-danger"></i>
                                        </span>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                               id="address" name="address" value="{{ old('address') }}" 
                                               placeholder="Ex: Rua das Flores, 123 - Centro, São Paulo - SP" required>
                                        <button type="button" class="btn btn-outline-secondary" id="searchAddress">
                                            <i class="fas fa-search me-1"></i> Buscar
                                        </button>
                                    </div>
                                    @error('address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Digite o endereço completo e clique em buscar para encontrar no mapa</small>
                                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                                </div>

                                <!-- Coordenadas Atuais -->
                                <div class="alert alert-info py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <small class="fw-semibold">Coordenadas atuais:</small>
                                            <div id="coordinatesDisplay" class="text-muted">
                                                @if(old('latitude') && old('longitude'))
                                                    {{ old('latitude') }}, {{ old('longitude') }}
                                                @else
                                                    <span class="text-warning">Clique no mapa para definir a localização</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Coluna Direita - Mapa e Tipos de Cozinha -->
                            <div class="col-lg-6">
                                <!-- Mapa para Localização -->
                                <div class="mb-4">
                                    <label class="form-label fs-6 fw-semibold">
                                        <i class="fas fa-map-marked-alt me-1 text-success"></i>
                                        Localização no Mapa <span class="text-danger">*</span>
                                    </label>
                                    <div id="locationMap" style="height: 250px; border-radius: 8px; border: 2px solid #e9ecef;"></div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-mouse-pointer me-1"></i>
                                            Clique no mapa para definir a localização exata
                                        </small>
                                    </div>
                                </div>

                                <!-- Tipos de Cozinha -->
                                <div class="mb-4">
                                    <label class="form-label fs-6 fw-semibold">
                                        <i class="fas fa-utensils me-1 text-warning"></i>
                                        Tipos de Cozinha <span class="text-danger">*</span>
                                    </label>
                                    <div class="cuisine-grid">
                                        @php
                                            $cuisineTypes = [
                                                'Brasileira' => '',
                                                'Italiana' => '', 
                                                'Japonesa' => '🇯🇵',
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
                                                        <span class="cuisine-emoji">{{ $emoji }}</span>
                                                        {{ $type }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @error('cuisine_types')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-2 d-block">
                                        Selecione todos os tipos de cozinha servidos
                                    </small>
                                </div>

                                <!-- Upload de Fotos (OPCIONAL) -->
                                <div class="mb-4">
                                    <label for="photos" class="form-label fs-6 fw-semibold">
                                        <i class="fas fa-camera me-1 text-info"></i>
                                        Fotos do Restaurante <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <div class="file-upload-area border rounded p-4 text-center">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                        <p class="mb-2">Arraste e solte as fotos aqui ou</p>
                                        <input type="file" class="form-control d-none" 
                                               id="photos" name="photos[]" multiple accept="image/*">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('photos').click()">
                                            <i class="fas fa-folder-open me-1"></i> Selecionar Fotos
                                        </button>
                                        <small class="text-muted d-block mt-2">
                                            Até 10 fotos • JPEG, PNG, JPG, GIF, WEBP • Máx. 5MB cada
                                        </small>
                                    </div>
                                    @error('photos')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Preview das fotos -->
                                    <div id="photoPreview" class="row g-2 mt-3"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Dicas e Informações -->
                        <div class="alert alert-light border mt-4">
                            <div class="d-flex">
                                <i class="fas fa-lightbulb text-warning fa-2x me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Dicas para um cadastro perfeito:</h6>
                                    <ul class="mb-0 small text-muted">
                                        <li>Use um nome claro e atrativo</li>
                                        <li>Descreva bem o ambiente e especialidades</li>
                                        <li>Selecione todos os tipos de cozinha servidos</li>
                                        <li>Verifique se a localização está correta no mapa</li>
                                        <li>Fotos ajudam muito a atrair clientes!</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1 text-success"></i>
                                    Seus dados estão seguros conosco
                                </small>
                            </div>
                            <div class="d-flex gap-3">
                                <a href="{{ route('restaurants.map') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
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
.page-header {
    border-bottom: 3px solid #007bff;
    padding-bottom: 1rem;
}

.steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 60%;
    right: -40%;
    height: 2px;
    background: #dee2e6;
    z-index: 1;
}

.step.active:not(:last-child)::after {
    background: #007bff;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #dee2e6;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
}

.step.active .step-number {
    background: #007bff;
    color: white;
}

.step-label {
    font-size: 0.875rem;
    color: #6c757d;
    text-align: center;
}

.step.active .step-label {
    color: #007bff;
    font-weight: 500;
}

.cuisine-grid {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.cuisine-option {
    margin-bottom: 0.5rem;
}

.cuisine-option .form-check-input {
    margin-right: 0.5rem;
}

.cuisine-option .form-check-label {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.cuisine-option .form-check-label:hover {
    background: #e9ecef;
}

.cuisine-option .form-check-input:checked + .form-check-label {
    background: #007bff;
    color: white;
}

.cuisine-emoji {
    font-size: 1.2em;
    margin-right: 0.5rem;
}

.file-upload-area {
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
    background: #fafbfc;
}

.file-upload-area:hover {
    border-color: #007bff;
    background: #f8f9fe;
}

.photo-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.photo-preview-item img {
    transition: transform 0.3s ease;
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

#locationMap {
    transition: all 0.3s ease;
}

#locationMap.loading {
    opacity: 0.7;
    pointer-events: none;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
let map, marker, currentLocation;

// Inicializar mapa
function initMap() {
    console.log('🗺️ Inicializando mapa...');
    
    // Configuração inicial do mapa
    map = L.map('locationMap').setView([-23.5505, -46.6333], 13);

    // Adicionar camada do mapa
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    // Evento de clique no mapa
    map.on('click', function(e) {
        setMarkerLocation(e.latlng.lat, e.latlng.lng);
    });

    // Tentar geolocalização do usuário
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            
            // Adicionar marcador da localização atual
            L.marker([userLat, userLng])
                .addTo(map)
                .bindPopup('<i class="fas fa-user me-1"></i>Sua localização atual')
                .openPopup();
                
            // Centralizar no usuário se não houver coordenadas antigas
            if (!document.getElementById('latitude').value) {
                map.setView([userLat, userLng], 13);
            }
        });
    }

    // Se existem coordenadas antigas, colocar marcador
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        setMarkerLocation(parseFloat(oldLat), parseFloat(oldLng));
        map.setView([oldLat, oldLng], 15);
    }
}

// Definir localização do marcador
function setMarkerLocation(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    document.getElementById('coordinatesDisplay').innerHTML = 
        `<span class="text-success fw-semibold">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>`;

    // Remover marcador anterior
    if (marker) {
        map.removeLayer(marker);
    }

    // Adicionar novo marcador
    marker = L.marker([lat, lng]).addTo(map)
        .bindPopup('<i class="fas fa-map-pin me-1"></i>Localização do restaurante')
        .openPopup();
}

// Buscar endereço
document.getElementById('searchAddress').addEventListener('click', function() {
    const address = document.getElementById('address').value;
    
    if (!address) {
        showAlert('Por favor, digite um endereço para buscar.', 'warning');
        return;
    }

    // Mostrar loading
    const btn = this;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';
    btn.disabled = true;

    // Buscar via Nominatim
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                
                map.setView([lat, lng], 16);
                setMarkerLocation(lat, lng);
                
                // Atualizar endereço com o resultado da busca
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

// Preview das fotos
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
        
        // Verificar tamanho do arquivo (5MB)
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
                        <img src="${e.target.result}" class="img-thumbnail w-100" 
                             style="height: 100px; object-fit: cover;" alt="Preview">
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

// Remover preview de foto
function removePhotoPreview(button) {
    button.closest('.col-4').remove();
}

// Contador de caracteres
document.getElementById('description').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('charCount').textContent = charCount;
    
    if (charCount > 500) {
        this.value = this.value.substring(0, 500);
        document.getElementById('charCount').textContent = 500;
        showAlert('Limite de 500 caracteres atingido.', 'warning');
    }
});

// Mostrar alerta
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Validação do formulário
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

    // Mostrar loading no botão
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';
    submitBtn.disabled = true;
});

// Drag and drop para fotos
const fileUploadArea = document.querySelector('.file-upload-area');
const fileInput = document.getElementById('photos');

fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#007bff';
    fileUploadArea.style.background = '#f0f8ff';
});

fileUploadArea.addEventListener('dragleave', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#dee2e6';
    fileUploadArea.style.background = '#fafbfc';
});

fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#dee2e6';
    fileUploadArea.style.background = '#fafbfc';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        fileInput.dispatchEvent(new Event('change'));
    }
});

// Inicializar mapa quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Inicializar contador de caracteres
    const description = document.getElementById('description');
    document.getElementById('charCount').textContent = description.value.length;
});
</script>
@endpush
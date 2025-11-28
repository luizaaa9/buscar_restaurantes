@extends('layouts.app')

@section('title', 'Cadastrar Restaurante')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle"></i> 
                        Cadastrar Novo Restaurante
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data" id="restaurantForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Informações Básicas -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nome do Restaurante <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        Descrição <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Descreva o restaurante, especialidades, ambiente, etc.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">
                                        Endereço Completo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                               id="address" name="address" value="{{ old('address') }}" required>
                                        <button type="button" class="btn btn-outline-secondary" id="searchAddress">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Digite o endereço completo e clique em buscar para encontrar no mapa</small>
                                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                                </div>

                                <!-- Tipos de Cozinha -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Tipos de Cozinha <span class="text-danger">*</span>
                                    </label>
                                    <div class="row">
                                        @php
                                            $cuisineTypes = [
                                                'Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa',
                                                'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar',
                                                'Café', 'Sobremesas', 'Fast Food', 'Pizza', 'Churrascaria',
                                                'Portuguesa', 'Argentina', 'Peruana', 'Coreana', 'Tailandesa'
                                            ];
                                            $oldCuisines = old('cuisine_types', []);
                                        @endphp
                                        
                                        @foreach($cuisineTypes as $type)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="cuisine_types[]" value="{{ $type }}" 
                                                       id="cuisine_{{ Str::slug($type) }}"
                                                       {{ in_array($type, $oldCuisines) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cuisine_{{ Str::slug($type) }}">
                                                    {{ $type }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('cuisine_types')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Mapa para Localização -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Localização no Mapa <span class="text-danger">*</span>
                                    </label>
                                    <div id="locationMap" style="height: 250px; border-radius: 8px; border: 1px solid #ddd;"></div>
                                    <small class="text-muted d-block mt-1">
                                        Clique no mapa para definir a localização exata do restaurante
                                    </small>
                                    <div id="coordinates" class="mt-2 p-2 bg-light rounded">
                                        <small>
                                            <strong>Coordenadas:</strong> 
                                            <span id="latDisplay" class="text-primary">---</span>, 
                                            <span id="lngDisplay" class="text-primary">---</span>
                                        </small>
                                    </div>
                                    @error('latitude')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    @error('longitude')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Upload de Fotos -->
                                <div class="mb-3">
                                    <label for="photos" class="form-label">
                                        Fotos do Restaurante <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control @error('photos') is-invalid @enderror" 
                                           id="photos" name="photos[]" multiple accept="image/*" required>
                                    @error('photos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Selecione 1 a 10 fotos (JPEG, PNG, JPG, GIF, WEBP). Tamanho máximo: 5MB por foto.
                                    </small>
                                    
                                    <!-- Preview das fotos -->
                                    <div id="photoPreview" class="mt-3 row g-2"></div>
                                </div>

                                <!-- Dicas -->
                                <div class="alert alert-info">
                                    <h6 class="alert-heading"><i class="fas fa-lightbulb"></i> Dicas para um bom cadastro:</h6>
                                    <ul class="mb-0 small">
                                        <li>Use fotos de boa qualidade</li>
                                        <li>Descreva bem o ambiente e especialidades</li>
                                        <li>Selecione todos os tipos de cozinha servidos</li>
                                        <li>Verifique se a localização está correta no mapa</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save"></i> Cadastrar Restaurante
                                    </button>
                                </div>
                            </div>
                        </div>
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
    let map, marker, currentLocation;

    function initMap() {
        // Inicializar mapa centrado em São Paulo
        map = L.map('locationMap').setView([-23.5505, -46.6333], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Adicionar marcador ao clicar no mapa
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
                    .bindPopup('Sua localização atual')
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

    function setMarkerLocation(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('latDisplay').textContent = lat.toFixed(6);
        document.getElementById('lngDisplay').textContent = lng.toFixed(6);

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng]).addTo(map)
            .bindPopup('Localização do restaurante')
            .openPopup();
    }

    // Buscar endereço usando Nominatim (OpenStreetMap)
    document.getElementById('searchAddress').addEventListener('click', function() {
        const address = document.getElementById('address').value;
        
        if (!address) {
            alert('Por favor, digite um endereço para buscar.');
            return;
        }

        // Mostrar loading
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
                    
                    // Atualizar endereço com o resultado da busca
                    document.getElementById('address').value = data[0].display_name;
                } else {
                    alert('Endereço não encontrado. Tente um endereço mais específico.');
                }
            })
            .catch(error => {
                console.error('Erro ao buscar endereço:', error);
                alert('Erro ao buscar endereço. Tente novamente.');
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
            alert(`Você pode selecionar no máximo ${maxFiles} fotos.`);
            this.value = '';
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Verificar tamanho do arquivo (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert(`O arquivo "${file.name}" é muito grande. Tamanho máximo: 5MB.`);
                this.value = '';
                preview.innerHTML = '';
                return;
            }
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4';
                    col.innerHTML = `
                        <div class="photo-preview-item position-relative">
                            <img src="${e.target.result}" class="img-thumbnail w-100" style="height: 100px; object-fit: cover;">
                            <small class="d-block text-center text-truncate">${file.name}</small>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="removePhotoPreview(this)">
                                <i class="fas fa-times"></i>
                            </button>
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

    // Validação do formulário
    document.getElementById('restaurantForm').addEventListener('submit', function(e) {
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        const cuisineTypes = document.querySelectorAll('input[name="cuisine_types[]"]:checked');
        const photos = document.getElementById('photos').files;

        let isValid = true;
        let errorMessage = '';

        if (!latitude || !longitude) {
            isValid = false;
            errorMessage = 'Por favor, selecione a localização do restaurante no mapa.';
        } else if (cuisineTypes.length === 0) {
            isValid = false;
            errorMessage = 'Por favor, selecione pelo menos um tipo de cozinha.';
        } else if (photos.length === 0) {
            isValid = false;
            errorMessage = 'Por favor, selecione pelo menos uma foto do restaurante.';
        }

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }

        // Mostrar loading no botão
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';
        submitBtn.disabled = true;
    });

    // Inicializar mapa quando a página carregar
    document.addEventListener('DOMContentLoaded', initMap);
</script>

<style>
.photo-preview-item {
    position: relative;
}
.photo-preview-item .btn {
    transform: translate(50%, -50%);
}
#locationMap {
    z-index: 1;
}
</style>
@endpush
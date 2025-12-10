@extends('layouts.app')

@section('title', 'Restaurantes Próximos')

@section('content')
<div class="container-fluid px-0">
    <div class="restaurant-map-container">
        <div class="map-header bg-dark-gray p-4 border-bottom border-burgundy">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="h2 fw-bold text-white mb-2">
                            <i class="fas fa-map-marked-alt me-2 text-burgundy"></i>
                            Restaurantes Próximos
                        </h1>
                        <p class="text-light-gray mb-0">Encontre restaurantes perto de você</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('restaurants.index') }}" class="btn btn-outline-burgundy">
                            <i class="fas fa-list me-1"></i> Lista Completa
                        </a>
                        <a href="{{ route('restaurants.create') }}" class="btn btn-burgundy">
                            <i class="fas fa-plus me-1"></i> Adicionar
                        </a>
                    </div>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-light-gray mb-2">
                            <i class="fas fa-ruler me-1 text-burgundy"></i>Distância Máxima
                        </label>
                        <div class="input-group">
                            <input type="range" class="form-range" id="distanceRange" 
                                   min="1" max="50" value="10" step="1"
                                   onchange="updateDistanceValue(this.value)">
                            <span class="input-group-text bg-dark border-dark" style="min-width: 70px;">
                                <span id="distanceValue" class="text-white">10 km</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-light-gray mb-2">
                            <i class="fas fa-utensils me-1 text-burgundy"></i>Tipo de Cozinha
                        </label>
                        <select class="form-select border-dark bg-dark text-light-gray" id="cuisineFilter">
                            <option value="">Todas as cozinhas</option>
                            @foreach(['Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa', 
                                     'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar'] as $cuisine)
                                <option value="{{ $cuisine }}">{{ $cuisine }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-light-gray mb-2">
                            <i class="fas fa-star me-1 text-burgundy"></i>Avaliação Mínima
                        </label>
                        <select class="form-select border-dark bg-dark text-light-gray" id="ratingFilter">
                            <option value="0">Qualquer avaliação</option>
                            <option value="3">★ 3.0+</option>
                            <option value="4">★ 4.0+</option>
                            <option value="4.5">★ 4.5+</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-lg-8">
                    <div id="map" style="height: calc(100vh - 200px);"></div>
                </div>
                
                <div class="col-lg-4 bg-dark-gray border-start border-dark">
                    <div class="restaurants-sidebar h-100">
                        <div class="sidebar-header p-3 border-bottom border-dark">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-utensils me-2 text-burgundy"></i>
                                Restaurantes Próximos
                                <small class="text-muted ms-2">(<span id="restaurantCount">{{ count($restaurants) }}</span>)</small>
                            </h5>
                        </div>
                        
                        <div class="restaurants-list" id="restaurantsList" style="height: calc(100vh - 250px); overflow-y: auto;">
                            @foreach($restaurants as $restaurant)
                            <div class="restaurant-item" 
                                 data-id="{{ $restaurant['id'] }}"
                                 data-name="{{ $restaurant['name'] }}"
                                 data-lat="{{ $restaurant['latitude'] }}"
                                 data-lng="{{ $restaurant['longitude'] }}"
                                 data-cuisines="{{ implode(',', $restaurant['cuisine_types']) }}"
                                 data-rating="{{ $restaurant['average_rating'] }}"
                                 data-distance="{{ $restaurant['distance'] }}"
                                 onclick="focusOnRestaurant({{ $restaurant['latitude'] }}, {{ $restaurant['longitude'] }}, '{{ $restaurant['name'] }}')">
                                
                                <div class="restaurant-info p-3 border-bottom border-dark">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0 fw-semibold text-white">{{ $restaurant['name'] }}</h6>
                                        <span class="badge bg-burgundy">
                                            <i class="fas fa-location-arrow me-1"></i>
                                            {{ $restaurant['distance_display'] }}
                                        </span>
                                    </div>
                                    
                                    <div class="rating mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($restaurant['average_rating']))
                                                <i class="fas fa-star text-warning small"></i>
                                            @elseif($i - 0.5 <= $restaurant['average_rating'])
                                                <i class="fas fa-star-half-alt text-warning small"></i>
                                            @else
                                                <i class="far fa-star text-medium-gray small"></i>
                                            @endif
                                        @endfor
                                        <small class="text-muted ms-2">
                                            {{ number_format($restaurant['average_rating'], 1) }} ({{ $restaurant['total_reviews'] }})
                                        </small>
                                    </div>
                                    
                                    <p class="address text-light-gray small mb-2">
                                        <i class="fas fa-map-marker-alt me-1 text-burgundy"></i>
                                        {{ Str::limit($restaurant['address'], 40) }}
                                    </p>
                                    
                                    <div class="cuisine-tags">
                                        @foreach(array_slice($restaurant['cuisine_types'], 0, 2) as $cuisine)
                                            <span class="badge bg-medium-gray text-light-gray me-1 mb-1">{{ $cuisine }}</span>
                                        @endforeach
                                        @if(count($restaurant['cuisine_types']) > 2)
                                            <span class="badge bg-dark-gray text-light-gray">+{{ count($restaurant['cuisine_types']) - 2 }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="{{ route('restaurants.show', $restaurant['id']) }}" 
                                           class="btn btn-sm btn-outline-burgundy w-100">
                                            <i class="fas fa-eye me-1"></i> Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @if(count($restaurants) === 0)
                            <div class="text-center py-5">
                                <i class="fas fa-utensils fa-4x text-medium-gray mb-3"></i>
                                <h5 class="text-light-gray mb-3">Nenhum restaurante próximo</h5>
                                <p class="text-muted">Não há restaurantes cadastrados próximos à sua localização.</p>
                                <a href="{{ route('restaurants.create') }}" class="btn btn-burgundy">
                                    <i class="fas fa-plus me-2"></i> Cadastrar Restaurante
                                </a>
                            </div>
                            @endif
                        </div>
                       
                        <div class="sidebar-footer p-3 border-top border-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1 text-burgundy"></i>
                                    Ordenado por distância
                                </small>
                                <button class="btn btn-sm btn-outline-burgundy" onclick="refreshRestaurants()">
                                    <i class="fas fa-sync-alt me-1"></i> Atualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="restaurantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark-gray border border-burgundy">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title text-white" id="modalRestaurantName"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-light-gray" id="modalRestaurantAddress"></p>
                        <div class="mb-3">
                            <span class="badge bg-burgundy me-2" id="modalRestaurantDistance"></span>
                            <span class="badge bg-medium-gray" id="modalRestaurantRating"></span>
                        </div>
                        <div id="modalRestaurantCuisines"></div>
                    </div>
                    <div class="col-md-6">
                        <p class="text-light-gray" id="modalRestaurantDescription"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-dark">
                <a href="#" class="btn btn-burgundy" id="modalRestaurantLink">
                    <i class="fas fa-external-link-alt me-1"></i> Ver Página Completa
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.restaurant-map-container {
    min-height: 100vh;
    background: #000000;
}

.map-header {
    background: #1a1a1a;
    border-bottom: 3px solid #800020;
}

#map {
    width: 100%;
    z-index: 1;
}

.restaurants-sidebar {
    background: #1a1a1a;
}

.restaurant-item {
    cursor: pointer;
    transition: all 0.3s ease;
    background: transparent;
}

.restaurant-item:hover {
    background: rgba(128, 0, 32, 0.1);
}

.restaurant-item.active {
    background: rgba(128, 0, 32, 0.2);
}

.rating {
    color: #ffc107;
}

.cuisine-tags .badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
}

.restaurants-list::-webkit-scrollbar {
    width: 8px;
}

.restaurants-list::-webkit-scrollbar-track {
    background: #2d2d2d;
    border-radius: 4px;
}

.restaurants-list::-webkit-scrollbar-thumb {
    background: #800020;
    border-radius: 4px;
}

.restaurants-list::-webkit-scrollbar-thumb:hover {
    background: #600018;
}

.map-marker {
    background: #800020;
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border: 3px solid white;
    box-shadow: 0 3px 6px rgba(0,0,0,0.4);
}

.map-marker-user {
    background: #28a745;
    border-color: white;
}

.map-popup {
    min-width: 250px;
    color: #333;
}

.map-popup .btn-burgundy {
    background-color: #800020;
    border-color: #800020;
    color: white;
    transition: all 0.3s ease;
}

.map-popup .btn-burgundy:hover {
    background-color: #600018;
    border-color: #600018;
}

.form-range::-webkit-slider-thumb {
    background: #800020;
    border: none;
}

.form-range::-moz-range-thumb {
    background: #800020;
    border: none;
}

.form-range::-ms-thumb {
    background: #800020;
    border: none;
}

.form-range::-webkit-slider-track {
    background: #333;
}

.form-range::-moz-range-track {
    background: #333;
}

.form-range::-ms-track {
    background: #333;
}

.btn-outline-burgundy {
    color: #800020;
    border-color: #800020;
    transition: all 0.3s ease;
}

.btn-outline-burgundy:hover {
    background-color: #800020;
    border-color: #800020;
    color: white;
}

.btn-burgundy {
    background-color: #800020;
    border-color: #800020;
    color: white;
    transition: all 0.3s ease;
}

.btn-burgundy:hover {
    background-color: #600018;
    border-color: #600018;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
}

.form-select.bg-dark {
    background-color: #1a1a1a !important;
    border-color: #444 !important;
    color: #cccccc !important;
}

.form-select.bg-dark:focus {
    border-color: #800020;
    box-shadow: 0 0 0 0.25rem rgba(128, 0, 32, 0.25);
}

.input-group-text.bg-dark {
    background-color: #1a1a1a !important;
    border-color: #444 !important;
}

.badge.bg-burgundy {
    background-color: #800020 !important;
    color: white;
    font-weight: 500;
    padding: 5px 10px;
}

.badge.bg-medium-gray {
    background-color: #333 !important;
    color: #ccc;
}

.badge.bg-dark-gray {
    background-color: #1a1a1a !important;
    color: #ccc;
}

.text-burgundy {
    color: #800020 !important;
}

.text-light-gray {
    color: #cccccc !important;
}

.text-muted {
    color: #888888 !important;
}

.border-dark {
    border-color: #444 !important;
}

.border-burgundy {
    border-color: #800020 !important;
}

.leaflet-popup-content-wrapper {
    background: #1a1a1a;
    color: #cccccc;
    border-radius: 8px;
    border: 2px solid #800020;
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}

.leaflet-popup-content {
    margin: 15px;
    font-family: inherit;
}

.leaflet-popup-tip {
    background: #1a1a1a;
}

.leaflet-marker-icon:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
    z-index: 1000 !important;
}

@media (max-width: 992px) {
    .col-lg-8, .col-lg-4 {
        width: 100%;
    }
    
    .col-lg-4 {
        height: 400px;
        border-left: none !important;
        border-top: 1px solid #444;
    }
    
    #map {
        height: 400px !important;
    }
    
    .restaurants-list {
        height: 300px !important;
    }
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .map-header {
        padding: 1rem !important;
    }
    
    .row.g-3 {
        margin-top: 1rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
let map;
let userMarker;
let restaurantMarkers = [];
let userLocation = {
    lat: {{ $userLat }},
    lng: {{ $userLng }},
    accuracy: null
};

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    getUserLocation();
    
    document.getElementById('cuisineFilter').addEventListener('change', filterRestaurants);
    document.getElementById('ratingFilter').addEventListener('change', filterRestaurants);
});

function initMap() {
    if (typeof L === 'undefined') {
        console.error('Leaflet não carregado');
        showAlert('Erro ao carregar o mapa. Recarregue a página.', 'danger');
        return;
    }
    
    map = L.map('map').setView([userLocation.lat, userLocation.lng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    addUserMarker();
    addRestaurantMarkers();
}

function addUserMarker() {
    if (userMarker) {
        map.removeLayer(userMarker);
    }
    
    userMarker = L.marker([userLocation.lat, userLocation.lng], {
        icon: L.divIcon({
            html: '<div class="map-marker map-marker-user"><i class="fas fa-user"></i></div>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            className: 'user-marker'
        })
    }).addTo(map);
    
    userMarker.bindPopup(`
        <div class="text-center">
            <h6 class="fw-bold mb-1 text-dark"><i class="fas fa-user me-1"></i>Sua Localização</h6>
            <small class="text-muted">Lat: ${userLocation.lat.toFixed(6)}<br>Lng: ${userLocation.lng.toFixed(6)}</small>
        </div>
    `);
}

function addRestaurantMarkers() {
    restaurantMarkers.forEach(marker => map.removeLayer(marker));
    restaurantMarkers = [];
    
    const restaurantItems = document.querySelectorAll('.restaurant-item');
    
    restaurantItems.forEach(item => {
        const lat = parseFloat(item.dataset.lat);
        const lng = parseFloat(item.dataset.lng);
        const name = item.dataset.name;
        const distance = item.dataset.distance;
        
        if (!isNaN(lat) && !isNaN(lng)) {
            const rating = parseFloat(item.dataset.rating);
            const markerColor = getMarkerColor(rating);
            
            const marker = L.marker([lat, lng], {
                icon: L.divIcon({
                    html: `
                        <div class="map-marker" style="background-color: ${markerColor}">
                            <i class="fas fa-utensils"></i>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    className: 'restaurant-marker'
                })
            }).addTo(map);
            
            const popupContent = `
                <div class="map-popup">
                    <h6 class="fw-bold mb-2">${name}</h6>
                    <div class="rating mb-2">
                        ${getStarRatingHTML(rating)}
                        <small class="text-muted ms-1">${rating.toFixed(1)}</small>
                    </div>
                    <p class="mb-2 small">${distance < 1 ? (distance * 1000).toFixed(0) + ' m' : distance.toFixed(1) + ' km'} de distância</p>
                    <button class="btn btn-sm btn-burgundy w-100 mt-2" onclick="showRestaurantDetails(${item.dataset.id})">
                        <i class="fas fa-info-circle me-1"></i> Ver Detalhes
                    </button>
                </div>
            `;
            
            marker.bindPopup(popupContent);
            
            marker.on('click', function() {
                focusOnRestaurant(lat, lng, name);
                highlightRestaurantItem(item);
            });
            
            restaurantMarkers.push(marker);
        }
    });
}

function getUserLocation() {
    if (!navigator.geolocation) {
        showAlert('Geolocalização não suportada', 'warning');
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            userLocation.lat = position.coords.latitude;
            userLocation.lng = position.coords.longitude;
            userLocation.accuracy = position.coords.accuracy;
            
            showAlert('Localização obtida com sucesso', 'success');
            
            if (map) {
                map.setView([userLocation.lat, userLocation.lng], 13);
                addUserMarker();
                fetchNearbyRestaurants();
            }
        },
        function(error) {
            let message = 'Erro ao obter localização: ';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message += 'Permissão negada';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message += 'Localização indisponível';
                    break;
                case error.TIMEOUT:
                    message += 'Tempo esgotado';
                    break;
                default:
                    message += 'Erro desconhecido';
            }
            showAlert(message, 'danger');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

function fetchNearbyRestaurants() {
    const radius = document.getElementById('distanceRange').value;
    
    fetch(`/api/nearby-restaurants?latitude=${userLocation.lat}&longitude=${userLocation.lng}&radius=${radius}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateRestaurantsList(data.data);
            }
        })
        .catch(error => {
            console.error('Erro ao buscar restaurantes:', error);
        });
}

function updateRestaurantsList(restaurants) {
    const container = document.getElementById('restaurantsList');
    const countElement = document.getElementById('restaurantCount');
    
    if (restaurants.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-utensils fa-4x text-medium-gray mb-3"></i>
                <h5 class="text-light-gray mb-3">Nenhum restaurante próximo</h5>
                <p class="text-muted">Não há restaurantes dentro do raio selecionado.</p>
            </div>
        `;
        countElement.textContent = '0';
        return;
    }
    
    let html = '';
    restaurants.forEach(restaurant => {
        const distanceDisplay = restaurant.distance < 1 
            ? Math.round(restaurant.distance * 1000) + ' m' 
            : restaurant.distance.toFixed(1) + ' km';
        
        html += `
            <div class="restaurant-item" 
                 data-id="${restaurant.id}"
                 data-name="${restaurant.name}"
                 data-lat="${restaurant.latitude}"
                 data-lng="${restaurant.longitude}"
                 data-cuisines="${restaurant.cuisine_types.join(',')}"
                 data-rating="${restaurant.average_rating}"
                 data-distance="${restaurant.distance}"
                 onclick="focusOnRestaurant(${restaurant.latitude}, ${restaurant.longitude}, '${restaurant.name.replace(/'/g, "\\'")}')">
                
                <div class="restaurant-info p-3 border-bottom border-dark">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0 fw-semibold text-white">${restaurant.name}</h6>
                        <span class="badge bg-burgundy">
                            <i class="fas fa-location-arrow me-1"></i>
                            ${distanceDisplay}
                        </span>
                    </div>
                    
                    <div class="rating mb-2">
                        ${getStarRatingHTML(restaurant.average_rating)}
                        <small class="text-muted ms-2">
                            ${restaurant.average_rating.toFixed(1)} (${restaurant.total_reviews})
                        </small>
                    </div>
                    
                    <p class="address text-light-gray small mb-2">
                        <i class="fas fa-map-marker-alt me-1 text-burgundy"></i>
                        ${restaurant.address.substring(0, 40)}${restaurant.address.length > 40 ? '...' : ''}
                    </p>
                    
                    <div class="cuisine-tags">
                        ${restaurant.cuisine_types.slice(0, 2).map(cuisine => 
                            `<span class="badge bg-medium-gray text-light-gray me-1 mb-1">${cuisine}</span>`
                        ).join('')}
                        ${restaurant.cuisine_types.length > 2 ? 
                            `<span class="badge bg-dark-gray text-light-gray">+${restaurant.cuisine_types.length - 2}</span>` : ''
                        }
                    </div>
                    
                    <div class="mt-3">
                        <a href="/restaurants/${restaurant.id}" 
                           class="btn btn-sm btn-outline-burgundy w-100">
                            <i class="fas fa-eye me-1"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    countElement.textContent = restaurants.length;
    addRestaurantMarkers();
}

function filterRestaurants() {
    const cuisineFilter = document.getElementById('cuisineFilter').value.toLowerCase();
    const ratingFilter = parseFloat(document.getElementById('ratingFilter').value);
    const distanceFilter = parseFloat(document.getElementById('distanceRange').value);
    
    const items = document.querySelectorAll('.restaurant-item');
    let visibleCount = 0;
    
    items.forEach(item => {
        const cuisines = item.dataset.cuisines.toLowerCase();
        const rating = parseFloat(item.dataset.rating);
        const distance = parseFloat(item.dataset.distance);
        
        const matchesCuisine = cuisineFilter === '' || cuisines.includes(cuisineFilter);
        const matchesRating = ratingFilter === 0 || rating >= ratingFilter;
        const matchesDistance = distance <= distanceFilter;
        
        if (matchesCuisine && matchesRating && matchesDistance) {
            item.style.display = 'block';
            visibleCount++;
            
            const lat = parseFloat(item.dataset.lat);
            const lng = parseFloat(item.dataset.lng);
            restaurantMarkers.forEach(marker => {
                const markerLatLng = marker.getLatLng();
                if (markerLatLng.lat === lat && markerLatLng.lng === lng) {
                    if (map.hasLayer(marker)) {
                        map.removeLayer(marker);
                    }
                    marker.addTo(map);
                }
            });
        } else {
            item.style.display = 'none';
            
            const lat = parseFloat(item.dataset.lat);
            const lng = parseFloat(item.dataset.lng);
            restaurantMarkers.forEach(marker => {
                const markerLatLng = marker.getLatLng();
                if (markerLatLng.lat === lat && markerLatLng.lng === lng) {
                    map.removeLayer(marker);
                }
            });
        }
    });
    
    document.getElementById('restaurantCount').textContent = visibleCount;
}

function focusOnRestaurant(lat, lng, name) {
    if (map) {
        map.setView([lat, lng], 16);
        
        const items = document.querySelectorAll('.restaurant-item');
        items.forEach(item => {
            item.classList.remove('active');
            if (parseFloat(item.dataset.lat) === lat && parseFloat(item.dataset.lng) === lng) {
                item.classList.add('active');
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        restaurantMarkers.forEach(marker => {
            const markerLatLng = marker.getLatLng();
            if (markerLatLng.lat === lat && markerLatLng.lng === lng) {
                marker.openPopup();
            }
        });
    }
}

function highlightRestaurantItem(clickedItem) {
    const items = document.querySelectorAll('.restaurant-item');
    items.forEach(item => {
        item.classList.remove('active');
    });
    clickedItem.classList.add('active');
    clickedItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function showRestaurantDetails(restaurantId) {
    const restaurantItem = document.querySelector(`.restaurant-item[data-id="${restaurantId}"]`);
    
    if (restaurantItem) {
        document.getElementById('modalRestaurantName').textContent = restaurantItem.dataset.name;
        document.getElementById('modalRestaurantAddress').textContent = restaurantItem.dataset.address;
        document.getElementById('modalRestaurantDistance').textContent = 
            restaurantItem.dataset.distance < 1 ? 
            Math.round(restaurantItem.dataset.distance * 1000) + ' m' : 
            parseFloat(restaurantItem.dataset.distance).toFixed(1) + ' km';
        document.getElementById('modalRestaurantRating').textContent = 
            '★ ' + parseFloat(restaurantItem.dataset.rating).toFixed(1);
        document.getElementById('modalRestaurantLink').href = `/restaurants/${restaurantId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('restaurantModal'));
        modal.show();
    }
}

function updateDistanceValue(value) {
    document.getElementById('distanceValue').textContent = value + ' km';
    filterRestaurants();
}

function refreshRestaurants() {
    getUserLocation();
}

function getMarkerColor(rating) {
    if (rating >= 4) return '#28a745';
    if (rating >= 3) return '#ffc107';
    return '#dc3545';
}

function getStarRatingHTML(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(rating)) {
            html += '<i class="fas fa-star text-warning"></i>';
        } else if (i - 0.5 <= rating) {
            html += '<i class="fas fa-star-half-alt text-warning"></i>';
        } else {
            html += '<i class="far fa-star text-medium-gray"></i>';
        }
    }
    return html;
}

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
</script>
@endpush
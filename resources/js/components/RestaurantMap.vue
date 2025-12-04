<template>
  <div class="restaurant-map-container">
    <div class="map-header bg-dark-gray p-4 mb-4 border-bottom border-burgundy">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <h1 class="h2 fw-bold text-white mb-2">
              <i class="fas fa-map-marked-alt me-2 text-burgundy"></i>
              Mapa de Restaurantes
            </h1>
            <p class="text-light-gray mb-0">Encontre restaurantes perto de você</p>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="/restaurants" class="btn btn-outline-burgundy">
              <i class="fas fa-list me-1"></i> Ver Lista
            </a>
            <a href="/restaurants/create" class="btn btn-burgundy">
              <i class="fas fa-plus me-1"></i> Adicionar
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="search-section mb-4">
        <div class="row g-3 align-items-end">
          <div class="col-md-5">
            <label class="form-label fw-semibold text-light-gray">Buscar Restaurantes</label>
            <div class="input-group">
              <span class="input-group-text bg-dark-gray border-dark">
                <i class="fas fa-search text-burgundy"></i>
              </span>
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Digite nome, endereço ou descrição..."
                class="form-control border-dark"
              >
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold text-light-gray">Filtrar por Cozinha</label>
            <select v-model="selectedCuisine" class="form-select border-dark">
              <option value="">Todas as cozinhas</option>
              <option v-for="cuisine in cuisineTypes" :key="cuisine" :value="cuisine">
                {{ cuisine }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <button @click="loadRestaurants" class="btn btn-outline-burgundy w-100" :disabled="loading">
              <i class="fas fa-sync-alt me-1" :class="{ 'fa-spin': loading }"></i>
              {{ loading ? 'Atualizando...' : 'Atualizar' }}
            </button>
          </div>
        </div>
      </div>

      <div class="map-content-wrapper">
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="card border-0 shadow-lg h-100">
              <div class="card-header bg-dark-gray border-bottom border-dark py-3">
                <h5 class="mb-0 text-white">
                  <i class="fas fa-map me-2 text-burgundy"></i>
                  Mapa Interativo
                  <small class="text-muted ms-2">({{ filteredRestaurants.length }} restaurantes)</small>
                </h5>
              </div>
              <div class="card-body p-0 position-relative">
                <div id="restaurant-map" ref="mapContainer" class="rounded-bottom"></div>
                <div v-if="!mapLoaded" class="map-loading-overlay">
                  <div class="spinner-border text-burgundy" role="status">
                    <span class="visually-hidden">Carregando mapa...</span>
                  </div>
                  <p class="mt-3 mb-0 text-light-gray">Carregando mapa...</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card border-0 shadow-lg h-100">
              <div class="card-header bg-dark-gray border-bottom border-dark py-3">
                <h5 class="mb-0 text-white">
                  <i class="fas fa-utensils me-2 text-burgundy"></i>
                  Lista de Restaurantes
                </h5>
              </div>
              <div class="card-body p-0">
                <div class="restaurant-list">
                  <div v-if="filteredRestaurants.length === 0" class="text-center py-5">
                    <i class="fas fa-search fa-4x text-medium-gray mb-3"></i>
                    <p class="text-light-gray mb-2">Nenhum restaurante encontrado</p>
                    <small class="text-muted">Tente ajustar os filtros de busca</small>
                  </div>
                  
                  <div v-else class="restaurant-items">
                    <div 
                      v-for="restaurant in filteredRestaurants" 
                      :key="restaurant.id"
                      class="restaurant-item"
                      :class="{ 
                        'active': selectedRestaurant?.id === restaurant.id,
                        'highlighted': restaurant.average_rating >= 4
                      }"
                      @click="focusOnRestaurant(restaurant)"
                    >
                      <div class="restaurant-info">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <h6 class="mb-0 fw-semibold text-white">{{ restaurant.name }}</h6>
                          <span v-if="restaurant.average_rating >= 4" class="badge bg-burgundy text-white">
                            <i class="fas fa-star me-1"></i>Destaque
                          </span>
                        </div>
                        
                        <div class="rating mb-2">
                          <span v-for="n in 5" :key="n" class="star" :class="{ 
                            'filled': n <= restaurant.average_rating,
                            'half-filled': n - 0.5 <= restaurant.average_rating && n > restaurant.average_rating
                          }">
                            ★
                          </span>
                          <small class="text-muted ms-2">
                            {{ restaurant.average_rating.toFixed(1) }} ({{ restaurant.total_reviews }} avaliações)
                          </small>
                        </div>

                        <p class="address text-light-gray small mb-2">
                          <i class="fas fa-map-marker-alt me-1 text-burgundy"></i>
                          {{ restaurant.address }}
                        </p>

                        <div class="cuisine-tags">
                          <span 
                            v-for="(cuisine, index) in restaurant.cuisine_types.slice(0, 2)" 
                            :key="index"
                            class="cuisine-tag"
                            :style="{
                              backgroundColor: getCuisineColor(cuisine),
                              color: 'white'
                            }"
                          >
                            {{ cuisine }}
                          </span>
                          <span v-if="restaurant.cuisine_types.length > 2" class="cuisine-tag-more">
                            +{{ restaurant.cuisine_types.length - 2 }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="selectedRestaurant" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header bg-burgundy text-white">
          <h4 class="modal-title mb-0">
            <i class="fas fa-info-circle me-2"></i>
            {{ selectedRestaurant.name }}
          </h4>
          <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
        </div>
        <div class="modal-body">
          <div class="restaurant-details">
            <div class="rating-section mb-4">
              <div class="d-flex align-items-center">
                <div class="stars me-3">
                  <span v-for="n in 5" :key="n" class="star large" :class="{ 
                    'filled': n <= selectedRestaurant.average_rating 
                  }">
                    ★
                  </span>
                </div>
                <div>
                  <span class="fw-bold text-burgundy fs-5">{{ selectedRestaurant.average_rating.toFixed(1) }}</span>
                  <small class="text-muted ms-1">({{ selectedRestaurant.total_reviews }} avaliações)</small>
                </div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <div class="info-item">
                  <label class="fw-semibold text-muted mb-1">
                    <i class="fas fa-map-marker-alt me-1 text-burgundy"></i>
                    Endereço
                  </label>
                  <p class="mb-0 text-dark">{{ selectedRestaurant.address }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-item">
                  <label class="fw-semibold text-muted mb-1">
                    <i class="fas fa-utensils me-1 text-burgundy"></i>
                    Tipos de Cozinha
                  </label>
                  <div class="cuisine-tags">
                    <span 
                      v-for="cuisine in selectedRestaurant.cuisine_types" 
                      :key="cuisine"
                      class="cuisine-tag"
                      :style="{
                        backgroundColor: getCuisineColor(cuisine),
                        color: 'white'
                      }"
                    >
                      {{ cuisine }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="info-item mt-3">
              <label class="fw-semibold text-muted mb-2">
                <i class="fas fa-align-left me-1 text-burgundy"></i>
                Sobre o Restaurante
              </label>
              <p class="mb-0 text-dark">{{ selectedRestaurant.description }}</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a :href="`/restaurants/${selectedRestaurant.id}`" class="btn btn-burgundy">
            <i class="fas fa-external-link-alt me-1"></i>
            Ver Página Completa
          </a>
          <button type="button" class="btn btn-outline-secondary" @click="closeModal">
            <i class="fas fa-times me-1"></i>
            Fechar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RestaurantMap',
  props: {
    restaurants: {
      type: Array,
      required: true,
      default: () => []
    },
    initialLat: {
      type: Number,
      default: -23.5505
    },
    initialLng: {
      type: Number,
      default: -46.6333
    },
    initialZoom: {
      type: Number,
      default: 12
    }
  },
  data() {
    return {
      map: null,
      L: null,
      markers: [],
      searchQuery: '',
      selectedCuisine: '',
      selectedRestaurant: null,
      loading: false,
      mapLoaded: false,
      cuisineTypes: [
        'Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa',
        'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar'
      ],
      cuisineColors: {
        'Brasileira': '#800020',
        'Italiana': '#8B0000', 
        'Japonesa': '#B22222',
        'Mexicana': '#DC143C',
        'Chinesa': '#CD5C5C',
        'Árabe': '#A52A2A',
        'Francesa': '#1C1C1C',
        'Vegetariana': '#2E8B57',
        'Vegana': '#228B22',
        'Frutos do Mar': '#4682B4'
      }
    };
  },
  computed: {
    filteredRestaurants() {
      let filtered = this.restaurants;

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(restaurant => 
          restaurant.name.toLowerCase().includes(query) ||
          restaurant.description.toLowerCase().includes(query) ||
          restaurant.address.toLowerCase().includes(query)
        );
      }

      if (this.selectedCuisine) {
        filtered = filtered.filter(restaurant => 
          restaurant.cuisine_types && restaurant.cuisine_types.includes(this.selectedCuisine)
        );
      }

      return filtered;
    }
  },
  async mounted() {
    await this.loadLeaflet();
    this.initMap();
    this.addRestaurantsToMap();
  },
  watch: {
    filteredRestaurants() {
      if (this.L && this.map) {
        this.updateMapMarkers();
      }
    }
  },
  methods: {
    async loadLeaflet() {
      try {
        const leaflet = await import('leaflet');
        this.L = leaflet.default;
        
        delete this.L.Icon.Default.prototype._getIconUrl;
        this.L.Icon.Default.mergeOptions({
          iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
          iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
          shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        });
      } catch (error) {
        console.error('Erro ao carregar Leaflet:', error);
      }
    },

    initMap() {
      if (!this.L) return;

      try {
        this.map = this.L.map(this.$refs.mapContainer).setView([this.initialLat, this.initialLng], this.initialZoom);

        this.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors',
          maxZoom: 18
        }).addTo(this.map);

        this.mapLoaded = true;
      } catch (error) {
        console.error('Erro ao inicializar mapa:', error);
      }
    },

    addRestaurantsToMap() {
      if (!this.L || !this.map) return;

      this.markers.forEach(marker => this.map.removeLayer(marker));
      this.markers = [];

      this.filteredRestaurants.forEach(restaurant => {
        try {
          if (!restaurant.latitude || !restaurant.longitude) return;

          const customIcon = this.L.divIcon({
            html: this.getMarkerHtml(restaurant),
            iconSize: [45, 45],
            iconAnchor: [22, 45],
            className: 'custom-marker'
          });

          const marker = this.L.marker([restaurant.latitude, restaurant.longitude], { 
            icon: customIcon 
          }).addTo(this.map);

          marker.on('click', () => {
            this.selectedRestaurant = restaurant;
          });

          this.markers.push(marker);

        } catch (error) {
          console.error('Erro ao adicionar marcador:', error, restaurant);
        }
      });

      if (this.filteredRestaurants.length > 0 && this.markers.length > 0) {
        try {
          const group = this.L.featureGroup(this.markers);
          this.map.fitBounds(group.getBounds().pad(0.1));
        } catch (error) {
          console.error('Erro ao ajustar view:', error);
        }
      }
    },

    updateMapMarkers() {
      this.addRestaurantsToMap();
    },

    getMarkerHtml(restaurant) {
      const rating = restaurant.average_rating || 0;
      const ratingColor = rating >= 4 ? '#28a745' : rating >= 3 ? '#ffc107' : '#dc3545';
      
      return `
        <div class="custom-marker-container">
          <div class="marker-pin">
            <div class="marker-content">
              <div class="rating-badge" style="background-color: ${ratingColor}">
                ${rating.toFixed(1)}
              </div>
              <div class="marker-icon text-burgundy">🍽️</div>
            </div>
          </div>
        </div>
      `;
    },

    getCuisineColor(cuisine) {
      return this.cuisineColors[cuisine] || '#6c757d';
    },

    focusOnRestaurant(restaurant) {
      this.selectedRestaurant = restaurant;
      if (this.map && restaurant.latitude && restaurant.longitude) {
        this.map.setView([restaurant.latitude, restaurant.longitude], 16);
      }
    },

    loadRestaurants() {
      this.loading = true;
      setTimeout(() => {
        this.addRestaurantsToMap();
        this.loading = false;
      }, 800);
    },

    closeModal() {
      this.selectedRestaurant = null;
    }
  }
};
</script>

<style scoped>
.restaurant-map-container {
  min-height: 100vh;
  background: var(--black);
}

.map-header {
  border-bottom: 3px solid var(--burgundy);
}

.search-section {
  background: var(--dark-gray);
  padding: 20px;
  border-radius: 10px;
  border: 1px solid var(--medium-gray);
}

.map-content-wrapper {
  margin-top: 20px;
}

#restaurant-map {
  height: 600px;
  border-radius: 0 0 8px 8px;
  background: var(--black);
}

.map-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(28, 28, 28, 0.95);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 0 0 8px 8px;
  z-index: 1000;
}

.restaurant-list {
  height: 600px;
  overflow-y: auto;
  background: var(--dark-gray);
}

.restaurant-items {
  padding: 15px;
}

.restaurant-item {
  background: var(--dark-gray);
  border: 2px solid transparent;
  border-radius: 10px;
  padding: 15px;
  margin-bottom: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid var(--medium-gray);
}

.restaurant-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(128, 0, 32, 0.2);
  border-color: var(--burgundy);
}

.restaurant-item.active {
  border-color: var(--burgundy);
  background: rgba(128, 0, 32, 0.1);
}

.restaurant-item.highlighted {
  border-left: 4px solid var(--burgundy);
}

.rating {
  display: flex;
  align-items: center;
  gap: 2px;
}

.star {
  color: var(--medium-gray);
  font-size: 14px;
  transition: color 0.2s ease;
}

.star.filled {
  color: #ffc107;
}

.star.half-filled {
  background: linear-gradient(90deg, #ffc107 50%, var(--medium-gray) 50%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.star.large {
  font-size: 24px;
}

.address {
  font-size: 13px;
  line-height: 1.4;
}

.cuisine-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 8px;
}

.cuisine-tag {
  padding: 4px 8px;
  border-radius: 15px;
  font-size: 11px;
  font-weight: 500;
}

.cuisine-tag-more {
  background: var(--medium-gray);
  color: white;
  padding: 4px 8px;
  border-radius: 15px;
  font-size: 11px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.modal-content {
  background: var(--dark-gray);
  border-radius: 12px;
  max-width: 600px;
  width: 100%;
  border: 2px solid var(--burgundy);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.modal-header {
  border-radius: 10px 10px 0 0;
  padding: 20px;
}

.modal-body {
  padding: 25px;
  color: var(--light-gray);
}

.modal-footer {
  padding: 20px;
  border-top: 1px solid var(--medium-gray);
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.info-item {
  margin-bottom: 15px;
}

.text-burgundy {
  color: var(--burgundy) !important;
}

.bg-burgundy {
  background-color: var(--burgundy) !important;
}

.border-burgundy {
  border-color: var(--burgundy) !important;
}

.restaurant-list::-webkit-scrollbar {
  width: 6px;
}

.restaurant-list::-webkit-scrollbar-track {
  background: var(--medium-gray);
  border-radius: 3px;
}

.restaurant-list::-webkit-scrollbar-thumb {
  background: var(--burgundy);
  border-radius: 3px;
}

.restaurant-list::-webkit-scrollbar-thumb:hover {
  background: #600018;
}

:deep(.custom-marker) {
  background: transparent;
  border: none;
}

:deep(.custom-marker-container) {
  position: relative;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
}

:deep(.marker-pin) {
  position: relative;
  cursor: pointer;
  transition: transform 0.2s ease;
}

:deep(.marker-pin:hover) {
  transform: scale(1.1);
}

:deep(.marker-content) {
  position: relative;
  text-align: center;
}

:deep(.rating-badge) {
  position: absolute;
  top: -8px;
  right: -8px;
  color: white;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  z-index: 1000;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

:deep(.marker-icon) {
  font-size: 28px;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}

@media (max-width: 768px) {
  #restaurant-map {
    height: 400px;
  }
  
  .restaurant-list {
    height: 400px;
  }
  
  .modal-overlay {
    padding: 10px;
  }
}
</style>
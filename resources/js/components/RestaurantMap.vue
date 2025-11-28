<template>
  <div class="restaurant-map-container">
    <div class="map-section">
      <div class="map-header">
        <h3>Mapa de Restaurantes</h3>
        <div class="map-controls">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Buscar restaurantes..."
            class="form-control"
            @input="searchRestaurants"
          >
          <select v-model="selectedCuisine" @change="filterRestaurants" class="form-select">
            <option value="">Todos os tipos</option>
            <option v-for="cuisine in cuisineTypes" :key="cuisine" :value="cuisine">
              {{ cuisine }}
            </option>
          </select>
        </div>
      </div>

      <div class="map-content">
        <!-- Mapa Leaflet -->
        <div id="restaurant-map" ref="mapContainer"></div>
        
        <!-- Lista de Restaurantes ao Lado do Mapa -->
        <div class="restaurant-list">
          <h5>Restaurantes ({{ filteredRestaurants.length }})</h5>
          <div class="restaurant-items">
            <div 
              v-for="restaurant in filteredRestaurants" 
              :key="restaurant.id"
              class="restaurant-item"
              :class="{ active: selectedRestaurant?.id === restaurant.id }"
              @click="focusOnRestaurant(restaurant)"
            >
              <div class="restaurant-info">
                <h6>{{ restaurant.name }}</h6>
                <div class="rating">
                  <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= restaurant.average_rating }">
                    ★
                  </span>
                  <small>({{ restaurant.average_rating }})</small>
                </div>
                <p class="address">{{ restaurant.address }}</p>
                <div class="cuisine-tags">
                  <span 
                    v-for="(cuisine, index) in restaurant.cuisine_types.slice(0, 2)" 
                    :key="index"
                    class="cuisine-tag"
                  >
                    {{ cuisine }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalhes do Restaurante -->
    <div v-if="selectedRestaurant" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ selectedRestaurant.name }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="restaurant-details">
              <div class="rating-section">
                <div class="stars">
                  <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= selectedRestaurant.average_rating }">
                    ★
                  </span>
                  <span class="rating-text">{{ selectedRestaurant.average_rating }} ({{ selectedRestaurant.total_reviews }} avaliações)</span>
                </div>
              </div>
              <p><strong>Endereço:</strong> {{ selectedRestaurant.address }}</p>
              <p><strong>Descrição:</strong> {{ selectedRestaurant.description }}</p>
              <div class="cuisine-section">
                <strong>Tipo de Cozinha:</strong>
                <div class="cuisine-tags">
                  <span 
                    v-for="cuisine in selectedRestaurant.cuisine_types" 
                    :key="cuisine"
                    class="cuisine-tag"
                  >
                    {{ cuisine }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a :href="`/restaurants/${selectedRestaurant.id}`" class="btn btn-primary">
              Ver Detalhes Completos
            </a>
            <button type="button" class="btn btn-secondary" @click="closeModal">Fechar</button>
          </div>
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
      required: true
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
      cuisineTypes: [
        'Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa',
        'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar'
      ]
    };
  },
  computed: {
    filteredRestaurants() {
      let filtered = this.restaurants;

      // Filtro por texto
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(restaurant => 
          restaurant.name.toLowerCase().includes(query) ||
          restaurant.description.toLowerCase().includes(query) ||
          restaurant.address.toLowerCase().includes(query)
        );
      }

      // Filtro por tipo de cozinha
      if (this.selectedCuisine) {
        filtered = filtered.filter(restaurant => 
          restaurant.cuisine_types.includes(this.selectedCuisine)
        );
      }

      return filtered;
    }
  },
  async mounted() {
    // Carregar Leaflet dinamicamente
    await this.loadLeaflet();
    this.initMap();
    this.addRestaurantsToMap();
  },
  watch: {
    filteredRestaurants() {
      if (this.L) {
        this.updateMapMarkers();
      }
    }
  },
  methods: {
    async loadLeaflet() {
      // Importar Leaflet dinamicamente
      const leaflet = await import('leaflet');
      this.L = leaflet.default;
      
      // Corrigir ícones do Leaflet
      delete this.L.Icon.Default.prototype._getIconUrl;
      this.L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
      });
    },

    initMap() {
      // Inicializar mapa
      this.map = this.L.map(this.$refs.mapContainer).setView([this.initialLat, this.initialLng], this.initialZoom);

      // Adicionar tile layer
      this.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
      }).addTo(this.map);
    },

    addRestaurantsToMap() {
      if (!this.L) return;

      // Limpar marcadores existentes
      this.markers.forEach(marker => this.map.removeLayer(marker));
      this.markers = [];

      // Adicionar novos marcadores
      this.filteredRestaurants.forEach(restaurant => {
        const customIcon = this.L.divIcon({
          html: this.getMarkerHtml(restaurant),
          iconSize: [40, 40],
          iconAnchor: [20, 40],
          className: 'custom-marker'
        });

        const marker = this.L.marker([restaurant.latitude, restaurant.longitude], { 
          icon: customIcon 
        }).addTo(this.map);

        marker.on('click', () => {
          this.selectedRestaurant = restaurant;
        });

        this.markers.push(marker);
      });

      // Ajustar view para mostrar todos os marcadores
      if (this.filteredRestaurants.length > 0) {
        const group = this.L.featureGroup(this.markers);
        this.map.fitBounds(group.getBounds().pad(0.1));
      }
    },

    updateMapMarkers() {
      this.addRestaurantsToMap();
    },

    getMarkerHtml(restaurant) {
      const rating = restaurant.average_rating;
      return `
        <div class="custom-marker-container" data-restaurant-id="${restaurant.id}">
          <div class="marker-pin">
            <div class="marker-content">
              <div class="rating-badge">${rating}</div>
              <div class="marker-icon">🍽️</div>
            </div>
          </div>
        </div>
      `;
    },

    focusOnRestaurant(restaurant) {
      this.selectedRestaurant = restaurant;
      this.map.setView([restaurant.latitude, restaurant.longitude], 15);
    },

    searchRestaurants() {
      // A busca é feita automaticamente pelo computed property
    },

    filterRestaurants() {
      // O filtro é feito automaticamente pelo computed property
    },

    closeModal() {
      this.selectedRestaurant = null;
    }
  }
};
</script>

<style scoped>
.restaurant-map-container {
  height: 100%;
}

.map-section {
  height: 70vh;
}

.map-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}

.map-controls {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.map-controls .form-control,
.map-controls .form-select {
  min-width: 200px;
}

.map-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 20px;
  height: calc(100% - 60px);
}

#restaurant-map {
  height: 100%;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.restaurant-list {
  background: white;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow-y: auto;
}

.restaurant-items {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.restaurant-item {
  padding: 12px;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.restaurant-item:hover,
.restaurant-item.active {
  border-color: #007bff;
  background-color: #f8f9fa;
}

.restaurant-info h6 {
  margin-bottom: 5px;
  color: #333;
}

.rating {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-bottom: 5px;
}

.star {
  color: #ddd;
  font-size: 14px;
}

.star.filled {
  color: #ffc107;
}

.address {
  font-size: 12px;
  color: #666;
  margin-bottom: 5px;
}

.cuisine-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.cuisine-tag {
  background: #e9ecef;
  color: #495057;
  padding: 2px 6px;
  border-radius: 12px;
  font-size: 11px;
}

/* Estilos para os marcadores customizados */
:deep(.custom-marker) {
  background: transparent;
  border: none;
}

:deep(.custom-marker-container) {
  position: relative;
}

:deep(.marker-pin) {
  position: relative;
  cursor: pointer;
}

:deep(.marker-content) {
  position: relative;
  text-align: center;
}

:deep(.rating-badge) {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ff6b6b;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  z-index: 1000;
}

:deep(.marker-icon) {
  font-size: 24px;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}

/* Modal styles */
.modal {
  display: block !important;
}

.restaurant-details .rating-section {
  margin-bottom: 15px;
}

.restaurant-details .stars {
  display: flex;
  align-items: center;
  gap: 5px;
}

.restaurant-details .star {
  font-size: 18px;
}

.restaurant-details .rating-text {
  margin-left: 10px;
  color: #666;
}

.cuisine-section {
  margin-top: 15px;
}

@media (max-width: 768px) {
  .map-content {
    grid-template-columns: 1fr;
    height: auto;
  }
  
  .restaurant-list {
    height: 300px;
  }

  .map-header {
    flex-direction: column;
    align-items: stretch;
  }

  .map-controls {
    flex-direction: column;
  }

  .map-controls .form-control,
  .map-controls .form-select {
    min-width: auto;
  }
}
</style>
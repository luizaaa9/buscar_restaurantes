import './bootstrap';
import { createApp } from 'vue';

import RestaurantMap from './components/RestaurantMap.vue';

console.log('Inicializando Vue app...');

const app = createApp({});

app.component('RestaurantMap', RestaurantMap);

app.mount('#vue-app');

console.log('Vue app montado com sucesso');
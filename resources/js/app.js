import './bootstrap';
import { createApp } from 'vue';

import RestaurantMap from './components/RestaurantMap.vue';

const app = createApp({});

app.component('RestaurantMap', RestaurantMap);

app.mount('#app');
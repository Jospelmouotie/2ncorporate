import '../css/app.css';
import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router'; // Assure-toi que ce fichier existe

// 1. Création de l'instance de l'application
const app = createApp(App);

// 2. Création et installation de Pinia (Gestion du panier)
const pinia = createPinia();
app.use(pinia);

// 3. Installation du Router (Navigation)
app.use(router);

// 4. Montage final
app.mount('#app');

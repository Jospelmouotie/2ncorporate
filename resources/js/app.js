import '../css/app.css';
import './bootstrap';

import axios from './axios' // Importez votre axios configuré
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
// CRUCIAL : Autorise l'envoi des cookies
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Optionnel mais recommandé :
axios.defaults.baseURL = 'http://localhost:8000';

const app = createApp(App);
const pinia = createPinia();
app.config.globalProperties.$axios = axios
app.use(pinia);
app.use(router);

app.mount('#app');

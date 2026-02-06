import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// AJOUTE CETTE LIGNE : Elle force Axios à envoyer les cookies de session
window.axios.defaults.withCredentials = true;

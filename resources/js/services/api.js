// resources/js/services/api.js
import axios from 'axios'

const api = axios.create({
  // Utilise la variable d'environnement définie dans ton .env
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  // Permet d'envoyer les cookies de session (nécessaire pour Sanctum)
  withCredentials: true,
})

// Tu peux aussi ajouter un intercepteur pour gérer les erreurs globales (ex: 401 non autorisé)
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      console.warn("Session expirée ou non autorisée");
      // Optionnel : rediriger vers /login
    }
    return Promise.reject(error);
  }
);

export default api

import axios from 'axios'

const api = axios.create({
  // Si VITE_API_BASE_URL n'est pas trouvé ou est erroné,
  // on utilise '/api' qui fonctionnera toujours sur Render
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
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

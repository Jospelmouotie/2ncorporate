import axios from 'axios'

// Configuration de base
axios.defaults.baseURL = '/api'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true // ← IMPORTANT pour les cookies

// Intercepteur pour les requêtes
axios.interceptors.request.use(config => {
    // Ajouter le token CSRF si disponible
    const token = document.querySelector('meta[name="csrf-token"]')
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.getAttribute('content')
    }

    // Ajouter l'acceptation JSON
    config.headers['Accept'] = 'application/json'

    return config
})

// Intercepteur pour les réponses
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Déconnexion automatique si non authentifié
            window.user = null
            if (!window.location.pathname.includes('/login')) {
                window.location.href = '/login'
            }
        }
        return Promise.reject(error)
    }
)

export default axios

// ColisFast Mobile API Handler
class MobileAPI {
    constructor() {
        // URL adaptée pour l'émulateur Android
        this.baseURL = 'http://10.0.2.2:8000';  // CHANGÉ : 10.0.2.2 au lieu de 127.0.0.1
        this.apiURL = this.baseURL + '/api';
        this.token = this.getStoredToken();
        
        // Configuration des headers par défaut
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    // Méthode pour gérer le storage (compatible avec l'environnement)
    getStoredToken() {
        try {
            return localStorage.getItem('auth_token');
        } catch (e) {
            // Fallback si localStorage n'est pas disponible
            return this.token || null;
        }
    }

    setStoredToken(token) {
        try {
            localStorage.setItem('auth_token', token);
        } catch (e) {
            // Fallback
            this.token = token;
        }
    }

    // Ajouter le token d'auth aux headers
    getAuthHeaders() {
        const headers = { ...this.defaultHeaders };
        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }
        return headers;
    }

    // Méthode générique pour les requêtes
    async request(endpoint, options = {}) {
        // Si l'endpoint commence par '/', utiliser baseURL web
        const url = endpoint.startsWith('http') ? endpoint : 
                   endpoint.startsWith('/api') ? `${this.baseURL}${endpoint}` :
                   endpoint.startsWith('/') ? `${this.baseURL}${endpoint}` :
                   `${this.apiURL}${endpoint}`;
        
        const config = {
            headers: this.getAuthHeaders(),
            ...options
        };

        try {
            console.log('Requesting:', url); // Debug
            const response = await fetch(url, config);
            
            if (!response.ok) {
                console.error('Response not OK:', response.status, response.statusText);
                throw new Error(`Erreur ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erreur API:', error);
            this.handleError(error);
            throw error;
        }
    }

    // GET request
    async get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    }

    // POST request
    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    // PUT request
    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    // DELETE request
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    // === AUTHENTIFICATION ===
    
    async login(email, password) {
        const data = await this.post('/login', { email, password });
        if (data.token) {
            this.token = data.token;
            this.setStoredToken(data.token);
            try {
                localStorage.setItem('user', JSON.stringify(data.user));
            } catch (e) {
                this.user = data.user;
            }
        }
        return data;
    }

    async register(userData) {
        const data = await this.post('/register', userData);
        if (data.token) {
            this.token = data.token;
            this.setStoredToken(data.token);
            try {
                localStorage.setItem('user', JSON.stringify(data.user));
            } catch (e) {
                this.user = data.user;
            }
        }
        return data;
    }

    async logout() {
        try {
            await this.post('/logout');
        } catch (error) {
            console.log('Erreur logout:', error);
        } finally {
            this.token = null;
            try {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
            } catch (e) {
                this.token = null;
                this.user = null;
            }
        }
    }

    // Obtenir l'utilisateur connecté
    getUser() {
        try {
            const user = localStorage.getItem('user');
            return user ? JSON.parse(user) : null;
        } catch (e) {
            return this.user || null;
        }
    }

    isAuthenticated() {
        return !!this.token && !!this.getUser();
    }

    // === LIVRAISONS SPÉCIFIQUES ===
    
    async getLivraisonsDisponibles() {
        // Route pour livreurs : /livreur/livraisons-disponible
        return this.get('/livreur/livraisons-disponible');
    }

    async getMesCommandes() {
        // Route pour livreurs : /livreur/mes-commandes
        return this.get('/livreur/mes-commandes');
    }

    async accepterCommande(id) {
        return this.post(`/livreur/commandes/${id}/accepter`);
    }

    async terminerCommande(id) {
        return this.post(`/livreur/commandes/${id}/terminer`);
    }

    // === AUTRES MÉTHODES ===
    
    async getCommandes() {
        return this.get('/commandes');
    }

    async createCommande(commandeData) {
        return this.post('/commandes', commandeData);
    }

    async getCommande(id) {
        return this.get(`/commandes/${id}`);
    }

    async confirmCommande(id) {
        return this.post(`/commandes/${id}/confirm`);
    }

    async getTokenBalance() {
        return this.get('/tokens/balance');
    }

    async purchaseTokens(amount) {
        return this.post('/tokens/purchase', { amount });
    }

    async updateLocation(latitude, longitude) {
        return this.post('/location/update', { latitude, longitude });
    }

    async saveFcmToken(fcmToken) {
        return this.post('/fcm-token', { fcm_token: fcmToken });
    }

    // === GESTION D'ERREURS ===
    
    handleError(error) {
        console.error('Erreur détaillée:', error);
        
        if (error.message.includes('401')) {
            this.logout();
            this.redirectToLogin();
        } else if (error.message.includes('403')) {
            this.showMessage('Accès refusé', 'error');
        } else if (error.message.includes('500')) {
            this.showMessage('Erreur serveur', 'error');
        } else if (error.message.includes('404')) {
            this.showMessage('Page non trouvée', 'error');
        } else {
            this.showMessage(error.message || 'Erreur réseau', 'error');
        }
    }

    // === UTILITAIRES ===
    
    redirectToLogin() {
        if (window.location.pathname !== '/login.html') {
            window.location.href = 'login.html';
        }
    }

    redirectToDashboard() {
        const user = this.getUser();
        if (user) {
            const dashboardUrls = {
                'client': 'dashboard-client.html',
                'livreur': 'dashboard-livreur.html', 
                'admin': 'dashboard-admin.html'
            };
            window.location.href = dashboardUrls[user.role] || 'dashboard-client.html';
        }
    }

    showMessage(message, type = 'info') {
        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white ${
            type === 'error' ? 'bg-red-500' : 
            type === 'success' ? 'bg-green-500' : 
            'bg-blue-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    formatPrice(price) {
        return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
    }

    formatDate(date) {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    }
}

// Instance globale de l'API
const mobileAPI = new MobileAPI();

// Vérifier l'authentification au chargement
document.addEventListener('DOMContentLoaded', () => {
    // Rediriger vers login si non authentifié (sauf sur les pages publiques)
    const publicPages = ['index.html', 'login.html', 'register.html', ''];
    const currentPage = window.location.pathname.split('/').pop();
    
    if (!publicPages.includes(currentPage) && !mobileAPI.isAuthenticated()) {
        mobileAPI.redirectToLogin();
    }
});

// Export pour utilisation dans d'autres scripts
window.mobileAPI = mobileAPI;
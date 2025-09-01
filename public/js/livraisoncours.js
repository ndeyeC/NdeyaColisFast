// public/js/livraisoncours.js - VERSION CORRIGÉE

class LivraisonManager {
    constructor(currentDeliveryId) {
        this.currentDeliveryId = currentDeliveryId;
        this.positionUpdateInterval = null;
        this.navigationMap = null;
        this.init();
    }

    init() {
        console.log('DOM loaded, currentDeliveryId:', this.currentDeliveryId);
        if (this.currentDeliveryId) {
            this.startPositionUpdates();
        }
        this.setupFormSubmissions();
        this.setupCancelForm();
    }

    startPositionUpdates() {
        if (this.positionUpdateInterval) clearInterval(this.positionUpdateInterval);
        this.positionUpdateInterval = setInterval(() => this.updateDeliveryPosition(), 30000);
        
        // Mise à jour périodique du statut
        setInterval(() => {
            if (this.currentDeliveryId) {
                this.fetchDeliveryStatus();
            }
        }, 60000);
    }

    updateDeliveryPosition() {
        if (!navigator.geolocation) return console.error('Géolocalisation non supportée');

        navigator.geolocation.getCurrentPosition(
            position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // CORRECTION: Utiliser la route web
                fetch(`/livreur/livraisons/${this.currentDeliveryId}/update-position`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        this.updateDeliveryStats(data);
                        this.updateMapPosition(lat, lng);
                    }
                })
                .catch(err => console.error('Erreur position:', err));
            },
            error => console.error('Erreur géolocalisation:', error),
            { enableHighAccuracy: true }
        );
    }

    fetchDeliveryStatus() {
        // CORRECTION: Utiliser la route web
        fetch(`/livreur/livraisons/${this.currentDeliveryId}/status`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.success) this.updateDeliveryStats(data);
            })
            .catch(err => console.error('Erreur refresh status:', err));
    }

    updateDeliveryStats(data) {
        if (data.distance_restante !== undefined) {
            const remainingElement = document.getElementById('remainingDistance');
            if (remainingElement) remainingElement.textContent = data.distance_restante + ' km';
        }

        if (data.temps_estime !== undefined) {
            const timeElement = document.getElementById('estimatedTime');
            if (timeElement) timeElement.textContent = data.temps_estime + ' min';
        }

        if (data.progress_percentage !== undefined) {
            const percentageElement = document.getElementById('deliveryProgressPercentage');
            const progressBarElement = document.getElementById('deliveryProgressBar');
            
            if (percentageElement) percentageElement.textContent = data.progress_percentage + '%';
            if (progressBarElement) progressBarElement.style.width = data.progress_percentage + '%';
        }
    }

    updateMapPosition(lat, lng) {
        console.log('Map position updated to:', lat, lng);
        if (this.navigationMap) {
            this.navigationMap.setView([lat, lng], this.navigationMap.getZoom());
        }
    }

    openNavigation(deliveryId) {
        console.log('Opening navigation for delivery:', deliveryId);

        fetch(`/livreur/livraisons/${deliveryId}/navigation`)
            .then(res => {
                console.log('Response status:', res.status);
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                console.log('Navigation data received:', data);

                if (!data.success) {
                    alert(data.message || 'Erreur lors de la récupération des données de navigation.');
                    return;
                }

                const routeData = data.route_data;
                console.log('Route data:', routeData);

                if (routeData.start_point.lat === routeData.end_point.lat &&
                    routeData.start_point.lng === routeData.end_point.lng) {
                    alert("Les adresses de départ et d'arrivée sont identiques. Veuillez vérifier les adresses.");
                    return;
                }

                this.openModal('navigationModal');

                setTimeout(() => {
                    this.initializeNavigationMap(routeData);
                }, 100);
            })
            .catch(err => {
                console.error('Navigation error:', err);
                alert('Erreur de navigation: ' + err.message);
            });
    }

    initializeNavigationMap(routeData) {
        const mapContainer = document.getElementById('leafletMap');
        
        if (!mapContainer) {
            console.error('Container leafletMap not found!');
            return;
        }

        if (this.navigationMap) {
            this.navigationMap.remove();
            this.navigationMap = null;
        }

        if (!routeData || !routeData.current_position || !routeData.start_point || !routeData.end_point) {
            console.error('Invalid route data:', routeData);
            alert('Données d\'itinéraire invalides');
            return;
        }

        console.log('Initializing map with data:', {
            current: routeData.current_position,
            start: routeData.start_point,
            end: routeData.end_point
        });

        try {
            this.navigationMap = L.map('leafletMap').setView([
                routeData.current_position.lat, 
                routeData.current_position.lng
            ], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.navigationMap);

            console.log('Base map initialized successfully');

            const startMarker = L.marker([routeData.start_point.lat, routeData.start_point.lng], {
                title: 'Point de départ'
            }).addTo(this.navigationMap);
            
            const endMarker = L.marker([routeData.end_point.lat, routeData.end_point.lng], {
                title: 'Point d\'arrivée'
            }).addTo(this.navigationMap);
            
            const currentMarker = L.marker([routeData.current_position.lat, routeData.current_position.lng], {
                title: 'Position actuelle'
            }).addTo(this.navigationMap);

            startMarker.bindPopup('🚀 Point de départ<br>' + (routeData.start_address || 'Adresse de départ'));
            endMarker.bindPopup('🎯 Point d\'arrivée<br>' + (routeData.end_address || 'Adresse d\'arrivée'));
            currentMarker.bindPopup('📍 Votre position actuelle');

            console.log('Markers added successfully');

            let polylineAdded = false;
            
            if (routeData.polyline && routeData.polyline.type === 'LineString' && routeData.polyline.coordinates) {
                try {
                    const coordinates = routeData.polyline.coordinates.map(coord => [coord[1], coord[0]]);
                    
                    const polyline = L.polyline(coordinates, {
                        color: '#007bff',
                        weight: 5,
                        opacity: 0.8
                    }).addTo(this.navigationMap);
                    
                    this.navigationMap.fitBounds(polyline.getBounds(), { padding: [20, 20] });
                    polylineAdded = true;
                    
                    console.log('Polyline added successfully with', coordinates.length, 'points');
                } catch (polylineError) {
                    console.error('Error adding polyline:', polylineError);
                }
            }

            if (!polylineAdded) {
                console.warn('No valid polyline data, using fallback');
                
                const fallbackPolyline = L.polyline([
                    [routeData.start_point.lat, routeData.start_point.lng],
                    [routeData.current_position.lat, routeData.current_position.lng],
                    [routeData.end_point.lat, routeData.end_point.lng]
                ], {
                    color: '#6c757d',
                    weight: 3,
                    dashArray: '5, 10',
                    opacity: 0.7
                }).addTo(this.navigationMap);
                
                this.navigationMap.fitBounds(fallbackPolyline.getBounds(), { padding: [20, 20] });
            }

            this.displayRouteInstructions(routeData);

            console.log('Map initialization completed successfully');

        } catch (error) {
            console.error('Error initializing map:', error);
            alert('Erreur lors de l\'initialisation de la carte: ' + error.message);
        }
    }

    displayRouteInstructions(routeData) {
        const instructionsDiv = document.getElementById('routeInstructions');
        if (!instructionsDiv) {
            console.warn('Instructions container not found');
            return;
        }

        let instructionsHTML = '';

        if (routeData.steps && Array.isArray(routeData.steps) && routeData.steps.length > 0) {
            instructionsHTML = routeData.steps.map((step, index) => {
                const distance = step.distance ? `${Math.round(step.distance)}m` : '';
                const duration = step.duration ? `${Math.round(step.duration / 60)}min` : '';
                const details = [distance, duration].filter(Boolean).join(', ');
                
                return `
                    <div class="flex items-start space-x-3 mb-3 p-2 bg-gray-50 rounded">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">
                            ${index + 1}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">${step.instruction || 'Instruction non disponible'}</p>
                            ${details ? `<p class="text-xs text-gray-500 mt-1">${details}</p>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            instructionsHTML = `
                <div class="text-center p-4">
                    <p class="text-sm text-gray-600 mb-2">📍 Instructions de base :</p>
                    <div class="space-y-2 text-sm">
                        <p>🚀 Départ : ${routeData.start_address || 'Point de départ'}</p>
                        <p>🎯 Arrivée : ${routeData.end_address || 'Point d\'arrivée'}</p>
                        ${routeData.distance_km ? `<p>📏 Distance : ${routeData.distance_km} km</p>` : ''}
                        ${routeData.duration_minutes ? `<p>⏱️ Durée estimée : ${routeData.duration_minutes} min</p>` : ''}
                    </div>
                </div>
            `;
        }

        instructionsDiv.innerHTML = instructionsHTML;
        console.log('Instructions displayed');
    }

    closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            
            if (id === 'navigationModal' && this.navigationMap) {
                setTimeout(() => {
                    this.navigationMap.remove();
                    this.navigationMap = null;
                    console.log('Navigation map cleaned up');
                }, 300);
            }
        }
    }

    startDelivery(deliveryId) {
        if (!navigator.geolocation) return alert('Activez la géolocalisation');

        const startBtn = document.querySelector(`button[onclick="startDelivery(${deliveryId})"]`);
        if (startBtn) {
            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Démarrage...';
        }

        navigator.geolocation.getCurrentPosition(
            position => {
                fetch(`/livreur/livraisons/${deliveryId}/demarrer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Erreur de démarrage');
                        if (startBtn) {
                            startBtn.disabled = false;
                            startBtn.innerHTML = '<i class="fas fa-play mr-2"></i> Démarrer';
                        }
                    }
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    alert('Erreur réseau');
                    if (startBtn) {
                        startBtn.disabled = false;
                        startBtn.innerHTML = '<i class="fas fa-play mr-2"></i> Démarrer';
                    }
                });
            },
            () => {
                alert('Position requise');
                if (startBtn) {
                    startBtn.disabled = false;
                    startBtn.innerHTML = '<i class="fas fa-play mr-2"></i> Démarrer';
                }
            },
            { enableHighAccuracy: true }
        );
    }

    markAsDelivered(deliveryId) {
        document.getElementById('deliveredForm').action = `/livreur/livraisons/${deliveryId}/marquer-livree`;
        this.openModal('deliveredModal');
    }

    showProblemModal(deliveryId) {
        document.getElementById('problemForm').action = `/livreur/livraisons/${deliveryId}/signaler-probleme`;
        this.openModal('problemModal');
    }

    cancelDelivery(deliveryId) {
        console.log('Tentative d\'annulation pour la livraison:', deliveryId);
        
        // Configurer le formulaire d'annulation
        const form = document.getElementById('cancelForm');
        form.action = `/livreur/livraisons/${deliveryId}/annuler`;
        form.reset();
        
        // Réinitialiser les messages d'erreur
        const errorContainer = document.getElementById('cancelFormErrors');
        if (errorContainer) {
            errorContainer.classList.add('hidden');
            errorContainer.innerHTML = '';
        }
        
        // Ouvrir la modal
        this.openModal('cancelModal');
    }

    setupCancelForm() {
        const cancelForm = document.getElementById('cancelForm');
        
        if (cancelForm) {
            cancelForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const formData = new FormData(cancelForm);
                const submitBtn = cancelForm.querySelector('button[type="submit"]');
                const submitText = submitBtn.querySelector('.submit-text');
                const originalText = submitText.textContent;
                
                // Afficher l'indicateur de chargement
                submitBtn.disabled = true;
                submitText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Traitement...';
                
                // Cacher les erreurs précédentes
                const errorContainer = document.getElementById('cancelFormErrors');
                if (errorContainer) {
                    errorContainer.classList.add('hidden');
                    errorContainer.innerHTML = '';
                }
                
                // Envoyer la requête
                fetch(cancelForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Afficher le message de succès
                        this.showAlert('success', data.message);
                        
                        // Fermer la modal et recharger la page
                        this.closeModal('cancelModal');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Afficher les erreurs
                        if (data.errors) {
                            let errorHtml = '<ul class="list-disc pl-5">';
                            for (const field in data.errors) {
                                data.errors[field].forEach(error => {
                                    errorHtml += `<li>${error}</li>`;
                                });
                            }
                            errorHtml += '</ul>';
                            
                            if (errorContainer) {
                                errorContainer.innerHTML = errorHtml;
                                errorContainer.classList.remove('hidden');
                            }
                        } else if (data.message) {
                            if (errorContainer) {
                                errorContainer.textContent = data.message;
                                errorContainer.classList.remove('hidden');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    if (errorContainer) {
                        errorContainer.textContent = 'Une erreur réseau est survenue';
                        errorContainer.classList.remove('hidden');
                    }
                })
                .finally(() => {
                    // Restaurer le bouton
                    submitBtn.disabled = false;
                    submitText.textContent = originalText;
                });
            });
        }
    }

    setupFormSubmissions() {
        const formsConfig = {
            'deliveredForm': { needsGeolocation: true },
            'problemForm': { needsGeolocation: true },
            'cancelForm': { needsGeolocation: false }
        };
        
        Object.entries(formsConfig).forEach(([formId, config]) => {
            const form = document.getElementById(formId);
            if (!form) {
                console.warn(`Formulaire ${formId} non trouvé.`);
                return;
            }
            
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                if (config.needsGeolocation && navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            this.updateOrCreateHiddenInput(form, 'latitude', position.coords.latitude);
                            this.updateOrCreateHiddenInput(form, 'longitude', position.coords.longitude);
                            console.log('Coordonnées obtenues:', {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            });
                            this.submitForm(form);
                        },
                        error => {
                            console.error('Erreur géolocalisation:', error);
                            let errorMessage = 'Impossible d\'obtenir la position. Veuillez activer la géolocalisation.';
                            if (error.code === error.PERMISSION_DENIED) {
                                errorMessage = 'Permission de géolocalisation refusée.';
                            } else if (error.code === error.POSITION_UNAVAILABLE) {
                                errorMessage = 'Position non disponible.';
                            } else if (error.code === error.TIMEOUT) {
                                errorMessage = 'Délai d\'obtention de la position dépassé.';
                            }
                            this.showAlert('error', errorMessage);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else if (config.needsGeolocation && !navigator.geolocation) {
                    this.showAlert('error', 'La géolocalisation n\'est pas supportée par votre navigateur.');
                } else {
                    this.submitForm(form);
                }
            });
        });
    }

    updateOrCreateHiddenInput(form, name, value) {
        let input = form.querySelector(`input[name="${name}"]`);
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }
        input.value = value;
    }

    submitForm(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const submitText = submitBtn.querySelector('.submit-text') || submitBtn;
        const originalText = submitText.textContent;
        
        const errorContainerMap = {
            'cancelForm': 'cancelFormErrors',
            'problemForm': 'problemFormErrors', 
            'deliveredForm': 'deliveredFormErrors'
        };
        const errorContainer = document.getElementById(errorContainerMap[form.id]);
        
        // Vérifier la connexion réseau
        if (!navigator.onLine) {
            this.showAlert('error', 'Aucune connexion réseau. Veuillez vérifier votre connexion.');
            return;
        }

        submitBtn.disabled = true;
        submitText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Envoi...';
        
        if (errorContainer) {
            errorContainer.classList.add('hidden');
            errorContainer.innerHTML = '';
        }

        console.log('Soumission du formulaire:', form.action, 'Données:', Object.fromEntries(formData));

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            console.log('Réponse reçue:', response.status, 'Content-Type:', response.headers.get('content-type'));
            
            // Vérifier si la réponse est JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Réponse non-JSON:', text);
                throw new Error('Réponse invalide du serveur (non-JSON).');
            }

            const data = await response.json();
            
            if (!response.ok) {
                if (response.status === 422) {
                    const errors = data.errors || {};
                    const errorMessages = Object.values(errors).flat().join('\n');
                    throw new Error(errorMessages || 'Données invalides.');
                } else if (response.status === 403) {
                    throw new Error('Accès refusé.');
                } else if (response.status === 404) {
                    throw new Error('Commande non trouvée.');
                } else if (response.status === 500) {
                    throw new Error(data.message || 'Erreur serveur.');
                } else {
                    throw new Error(data.message || `Erreur HTTP ${response.status}.`);
                }
            }

            if (data.success) {
                this.showAlert('success', data.message || 'Opération réussie');
                setTimeout(() => {
                    const modalMap = {
                        'cancelForm': 'cancelModal',
                        'problemForm': 'problemModal',
                        'deliveredForm': 'deliveredModal'
                    };
                    this.closeModal(modalMap[form.id]);
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Opération échouée.');
            }
        })
        .catch(error => {
            console.error('Erreur soumission:', error);
            const errorMessage = error.message || 'Une erreur inattendue est survenue.';
            
            if (errorContainer) {
                errorContainer.textContent = errorMessage;
                errorContainer.classList.remove('hidden');
            } else {
                this.showAlert('error', errorMessage);
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.textContent = originalText;
        });
    }

    openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
        }`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                ${message}
            </div>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

// Initialisation
let livraisonManager;

document.addEventListener('DOMContentLoaded', function() {
    const currentDeliveryId = document.querySelector('meta[name="current-delivery-id"]')?.content || null;
    livraisonManager = new LivraisonManager(currentDeliveryId);
    
    // Exposer les fonctions globales nécessaires
    window.openNavigation = (deliveryId) => livraisonManager.openNavigation(deliveryId);
    window.markAsDelivered = (deliveryId) => livraisonManager.markAsDelivered(deliveryId);
    window.showProblemModal = (deliveryId) => livraisonManager.showProblemModal(deliveryId);
    window.cancelDelivery = (deliveryId) => livraisonManager.cancelDelivery(deliveryId);
    window.startDelivery = (deliveryId) => livraisonManager.startDelivery(deliveryId);
    window.closeModal = (modalId) => livraisonManager.closeModal(modalId);
});
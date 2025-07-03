@extends('layouts.master')

@section('title', 'Navigation GPS Livreur')

@section('content')
<div class="container-fluid">
    <!-- Carte -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Navigation</h5>
                    <button id="refreshPosition" class="btn btn-sm btn-light">
                        <i class="fas fa-sync-alt me-1"></i>Actualiser
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails et Instructions -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-route me-2"></i>Détails</h6>
                </div>
                <div class="card-body" id="routeDetails">
                    <!-- Rempli dynamiquement -->
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-directions me-2"></i>Instructions</h6>
                </div>
                <div class="card-body p-0">
                    <div id="stepInstructions" class="list-group list-group-flush">
                        <!-- Rempli dynamiquement -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <button id="startNavigation" class="btn btn-primary w-100">
                                <i class="fas fa-play me-2"></i>Démarrer
                            </button>
                        </div>
                        <div class="col-md-4 mb-2">
                            <button id="completeDelivery" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>Terminer
                            </button>
                        </div>
                        <div class="col-md-4 mb-2">
                            <button class="btn btn-outline-secondary w-100">
                                <i class="fas fa-phone me-2"></i>Appeler Client
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<!-- Toastr -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Configuration
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right"
    };

    // Variables
    const commandeId = {{ $commande->id }};
    let map, routeControl, userMarker;
    let routeData = @json($route ?? null);
    let positionInterval;

    // Initialisation carte
    function initMap() {
        map = L.map('map').setView([14.716677, -17.467686], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Marqueur livreur
        userMarker = L.marker([14.716677, -17.467686], {
            icon: L.divIcon({
                html: '<i class="fas fa-truck text-primary fa-2x"></i>',
                iconSize: [30, 30],
                className: 'bg-transparent'
            })
        }).addTo(map).bindPopup("Votre position");
    }

    // Afficher l'itinéraire
    function displayRoute() {
        if (!routeData) return;

        if (routeControl) {
            map.removeControl(routeControl);
        }

        routeControl = L.Routing.control({
            waypoints: [
                L.latLng(routeData.start_point.lat, routeData.start_point.lng),
                L.latLng(routeData.end_point.lat, routeData.end_point.lng)
            ],
            routeWhileDragging: false,
            lineOptions: {styles: [{color: '#3b82f6', weight: 5}]}
        }).addTo(map);

        updateRouteDetails();
    }

    // Mettre à jour les détails
    function updateRouteDetails() {
        $('#routeDetails').html(`
            <div class="d-flex mb-3">
                <div class="text-primary me-3">
                    <i class="fas fa-map-marker-alt fa-2x"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted">Départ</p>
                    <p class="mb-0 fw-bold">${routeData.start_address || 'Non spécifié'}</p>
                </div>
            </div>
            <div class="d-flex mb-3">
                <div class="text-success me-3">
                    <i class="fas fa-flag-checkered fa-2x"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted">Arrivée</p>
                    <p class="mb-0 fw-bold">${routeData.end_address || 'Non spécifié'}</p>
                </div>
            </div>
            <hr>
            <div class="row text-center">
                <div class="col-4">
                    <h4 class="fw-bold text-primary">${routeData.distance_km || '0'} km</h4>
                    <p class="text-muted small">Distance</p>
                </div>
                <div class="col-4">
                    <h4 class="fw-bold text-primary">${routeData.duration_minutes || '0'} min</h4>
                    <p class="text-muted small">Durée</p>
                </div>
                <div class="col-4">
                    <h4 class="fw-bold text-primary">${new Date().getHours()}:${String(new Date().getMinutes()).padStart(2, '0')}</h4>
                    <p class="text-muted small">Heure estimée</p>
                </div>
            </div>
        `);

        // Instructions
        if (routeData.steps) {
            $('#stepInstructions').empty();
            routeData.steps.forEach((step, i) => {
                $('#stepInstructions').append(`
                    <div class="list-group-item">
                        <div class="d-flex">
                            <div class="me-3 text-muted">
                                ${i+1}.
                            </div>
                            <div>
                                <p class="mb-1">${step.instruction}</p>
                                <small class="text-muted">${step.distance} • ${step.duration}</small>
                            </div>
                        </div>
                    </div>
                `);
            });
        }
    }

    // Démarrer la navigation
    $('#startNavigation').click(function() {
        if (!navigator.geolocation) {
            toastr.error("Géolocalisation non supportée");
            return;
        }

        navigator.geolocation.getCurrentPosition(async (pos) => {
            try {
                const response = await axios.post(
                    "{{ route('livreur.commandes.start-tracking', $commande->id) }}", 
                    {
                        current_lat: pos.coords.latitude,
                        current_lng: pos.coords.longitude
                    }
                );

                routeData = response.data.route;
                displayRoute();
                startTracking();
                toastr.success("Navigation démarrée");
            } catch (error) {
                console.error(error);
                toastr.error("Erreur: " + (error.response?.data?.message || error.message));
            }
        }, (err) => {
            toastr.error("Erreur GPS: " + err.message);
        });
    });

    // Suivi position
    function startTracking() {
        if (positionInterval) clearInterval(positionInterval);
        
        positionInterval = setInterval(() => {
            navigator.geolocation.getCurrentPosition((pos) => {
                const {latitude, longitude} = pos.coords;
                userMarker.setLatLng([latitude, longitude]);
                
                axios.post(
                    "{{ route('livreur.commandes.update-position', $commande->id) }}",
                    {lat: latitude, lng: longitude}
                );
            });
        }, 10000); // Toutes les 10 secondes
    }

    // Terminer livraison
    $('#completeDelivery').click(function() {
        if (!confirm("Confirmer la fin de livraison ?")) return;

        axios.post("{{ route('livreur.commandes.complete', $commande->id) }}")
            .then(() => {
                toastr.success("Livraison terminée");
                setTimeout(() => window.location.href = "{{ route('livreur.commandes') }}", 1500);
            })
            .catch(error => {
                toastr.error("Erreur: " + error.response?.data?.message);
            });
    });

    // Actualiser position
    $('#refreshPosition').click(function() {
        navigator.geolocation.getCurrentPosition((pos) => {
            userMarker.setLatLng([pos.coords.latitude, pos.coords.longitude]);
            map.setView([pos.coords.latitude, pos.coords.longitude], 15);
            toastr.info("Position actualisée");
        });
    });

    // Initialisation
    initMap();
    if (routeData) displayRoute();
});
</script>

<style>
    #map { height: 100%; min-height: 500px; }
    .leaflet-routing-container { background: white; padding: 10px; }
</style>
@endsection
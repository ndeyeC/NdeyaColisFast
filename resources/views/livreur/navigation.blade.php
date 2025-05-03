@extends('layouts.master')

@section('title', 'Navigation GPS')

@section('page-title', 'Navigation GPS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Navigation en temps réel</h5>
                    <button class="btn btn-sm btn-light" id="refreshLocation">
                        <i class="fas fa-sync-alt me-1"></i>Actualiser
                    </button>
                </div>
                <div class="card-body p-0">
                    <!-- Zone de carte -->
                    <div id="map" style="height: 500px; width: 100%; background-color: #e9ecef;">
                        <!-- Image statique pour simuler la carte -->
                        <div class="position-relative h-100 d-flex align-items-center justify-content-center">
                            <img src="/images/map-placeholder.jpg" alt="Carte de navigation" class="img-fluid" style="max-height: 100%; width: 100%; object-fit: cover;">
                            
                            <!-- Overlay pour l'effet "en cours de chargement" -->
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light bg-opacity-75">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <h5>Chargement de la carte...</h5>
                                <p class="text-muted">Cette fonctionnalité sera active quand le backend sera implémenté.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-route me-2"></i>Détails de l'itinéraire</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Position actuelle</p>
                            <p class="fw-bold mb-0">Votre position • Avenue Cheikh Anta Diop</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start ps-5 my-2">
                        <div class="border-start border-2 border-primary" style="height: 40px;"></div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-flag-checkered"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Destination</p>
                            <p class="fw-bold mb-0">Résidence Les Mamelles, Ouakam, Dakar</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">3.2 km</h4>
                            <p class="text-muted small">Distance</p>
                        </div>
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">12 min</h4>
                            <p class="text-muted small">Temps estimé</p>
                        </div>
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">14:25</h4>
                            <p class="text-muted small">Arrivée estimée</p>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-primary" id="startNavigation">
                            <i class="fas fa-play-circle me-2"></i>Démarrer la navigation vocale
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-directions me-2"></i>Étapes de l'itinéraire</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center py-3">
                            <div class="me-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-arrow-right text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0">Continuez tout droit sur <strong>Avenue Cheikh Anta Diop</strong></p>
                                <p class="text-muted mb-0 small">800 mètres • 3 minutes</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center py-3">
                            <div class="me-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-arrow-right text-primary" style="transform: rotate(45deg);"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0">Tournez à droite sur <strong>Rue de Ouakam</strong></p>
                                <p class="text-muted mb-0 small">1.5 kilomètres • 5 minutes</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center py-3">
                            <div class="me-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-arrow-right text-primary" style="transform: rotate(-45deg);"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0">Tournez à gauche sur <strong>Avenue des Mamelles</strong></p>
                                <p class="text-muted mb-0 small">700 mètres • 2 minutes</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center py-3">
                            <div class="me-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-arrow-right text-primary" style="transform: rotate(45deg);"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0">Tournez à droite vers <strong>Résidence Les Mamelles</strong></p>
                                <p class="text-muted mb-0 small">200 mètres • 1 minute</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center py-3">
                            <div class="me-3">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-flag-checkered text-white"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0"><strong>Vous êtes arrivé à destination</strong></p>
                                <p class="text-muted mb-0 small">Résidence Les Mamelles, Ouakam, Dakar</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Options supplémentaires</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-outline-primary w-100">
                                <i class="fas fa-phone me-2"></i>Appeler le client
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-outline-success w-100">
                                <i class="fas fa-motorcycle me-2"></i>Mode moto
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('livreur.details-livraison') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-box me-2"></i>Détails de la livraison
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Scripts statiques pour la démo
    $(function() {
        $('#startNavigation').click(function() {
            alert('La navigation vocale sera activée une fois l\'intégration avec le backend terminée.');
        });
        
        $('#refreshLocation').click(function() {
            alert('Actualisation de la position en cours...');
        });
    });
</script>
@endsection
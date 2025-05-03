@extends('layouts.master')

@section('title', 'Détails de la livraison')

@section('page-title', 'Détails de la livraison')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>Informations de livraison #CF-2458</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-hourglass-half fa-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">Statut actuel</h6>
                                    <p class="text-success mb-0"><strong>En cours de livraison</strong></p>
                                </div>
                            </div>

                            <h6 class="mt-4 text-primary">Informations client</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Nom du client:</td>
                                            <td>Mamadou Diallo</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Téléphone:</td>
                                            <td>77 123 45 67</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Email:</td>
                                            <td>mamadou.diallo@example.com</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>Adresses</h6>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <div class="bg-info rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                    <small>A</small>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Point de collecte</p>
                                                <p class="mb-0">Boutique ElectroPlus, Plateau, Dakar</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-start px-4 py-2">
                                        <div class="border-start border-dashed border-2" style="height: 30px;"></div>
                                    </div>
                                    
                                    <div>
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <div class="bg-danger rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                    <small>B</small>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Point de livraison</p>
                                                <p class="mb-0">Résidence Les Mamelles, Ouakam, Dakar</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-box me-2"></i>Détails du colis</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold">Type de colis:</td>
                                                    <td>Électronique</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Poids:</td>
                                                    <td>2.5 kg</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Dimensions:</td>
                                                    <td>30 x 20 x 15 cm</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Instructions:</td>
                                                    <td>Manipuler avec précaution. Contient des appareils électroniques fragiles.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Informations de paiement</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold">Méthode de paiement:</td>
                                                    <td>Paiement à la livraison</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Montant à collecter:</td>
                                                    <td class="text-danger fw-bold">5,500 FCFA</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Frais de livraison:</td>
                                                    <td>1,200 FCFA</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Votre commission:</td>
                                                    <td class="text-success">850 FCFA</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Chronologie de la livraison</h6>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Commande acceptée</p>
                                                <p class="text-muted mb-0"><small>16 Mars 2025, 10:15</small></p>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">En route pour la collecte</p>
                                                <p class="text-muted mb-0"><small>16 Mars 2025, 10:30</small></p>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Colis collecté</p>
                                                <p class="text-muted mb-0"><small>16 Mars 2025, 11:05</small></p>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-motorcycle"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">En route pour la livraison</p>
                                                <p class="text-muted mb-0"><small>16 Mars 2025, 11:10</small></p>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex align-items-center text-muted">
                                            <div class="me-3">
                                                <div class="bg-light rounded-circle text-secondary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-home"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Livré</p>
                                                <p class="text-muted mb-0"><small>En attente</small></p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('livreur.navigation') }}" class="btn btn-primary">
                                    <i class="fas fa-map-marked-alt me-2"></i>Navigation GPS
                                </a>
                                
                                <div>
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#completeDeliveryModal">
                                        <i class="fas fa-check-circle me-2"></i>Confirmer la livraison
                                    </button>
                                    
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#problemModal">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Signaler un problème
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation Livraison -->
<div class="modal fade" id="completeDeliveryModal" tabindex="-1" aria-labelledby="completeDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="completeDeliveryModalLabel"><i class="fas fa-check-circle me-2"></i>Confirmer la livraison</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Paiement reçu?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentReceived" id="paymentYes" value="yes" checked>
                            <label class="form-check-label" for="paymentYes">
                                Oui, j'ai reçu 5,500 FCFA
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentReceived" id="paymentNo" value="no">
                            <label class="form-check-label" for="paymentNo">
                                Non, le client a payé en ligne
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="signature" class="form-label">Signature du client (optionnel)</label>
                        <div class="border p-3 mb-2 bg-light text-center" id="signatureArea" style="height: 150px;">
                            [Zone de signature]
                        </div>
                        <button type="button" class="btn btn-sm btn-light">Effacer</button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photoConfirmation" class="form-label">Photo de confirmation (optionnel)</label>
                        <input type="file" class="form-control" id="photoConfirmation">
                    </div>
                    
                    <div class="mb-3">
                        <label for="deliveryNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="deliveryNotes" rows="2" placeholder="Ajoutez des notes si nécessaire"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success"><i class="fas fa-check me-2"></i>Confirmer la livraison</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Problème -->
<div class="modal fade" id="problemModal" tabindex="-1" aria-labelledby="problemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="problemModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Signaler un problème</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="problemType" class="form-label">Type de problème</label>
                        <select class="form-select" id="problemType">
                            <option selected>Choisir le type de problème</option>
                            <option value="1">Adresse introuvable</option>
                            <option value="2">Client absent</option>
                            <option value="3">Colis endommagé</option>
                            <option value="4">Problème de paiement</option>
                            <option value="5">Autre problème</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="problemDescription" class="form-label">Description du problème</label>
                        <textarea class="form-control" id="problemDescription" rows="3" placeholder="Décrivez le problème rencontré"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="problemPhoto" class="form-label">Photo (optionnel)</label>
                        <input type="file" class="form-control" id="problemPhoto">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger"><i class="fas fa-paper-plane me-2"></i>Envoyer le rapport</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Scripts statiques pour la démo
    $(function() {
        // Code pour la visualisation interactive sera implémenté ici
        console.log('Détails de livraison chargés');
    });
</script>
@endsection
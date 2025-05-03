@extends('layouts.master')
@section('title', 'Profil Livreur')
@section('page-title', 'Mon Profil')
@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Carte du profil simplifié -->
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img src="{{ asset('assets/images/default-livreur.png') }}" alt="Photo de profil" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #4CAF50;">
                    </div>
                    <h4 class="mb-2 fw-bold">Amadou Diop</h4>
                    <p class="text-success mb-1"><i class="fas fa-star me-1"></i>4.8/5 (124 avis)</p>
                    <span class="badge bg-success mb-3">Disponible</span>
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <button class="btn btn-outline-success"><i class="fas fa-edit me-1"></i>Modifier</button>
                        <button class="btn btn-outline-danger"><i class="fas fa-power-off me-1"></i>Mode Hors-ligne</button>
                    </div>
                </div>
            </div>

            <!-- Informations personnelles essentielles -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Nom complet</label>
                        <p class="fw-medium">Amadou Diop</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Téléphone</label>
                        <p class="fw-medium">+221 77 123 45 67</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="fw-medium">amadou.diop@colisfast.sn</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Adresse</label>
                        <p class="fw-medium">Parcelles Assainies, Unité 15, Dakar</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Type de véhicule</label>
                        <p class="fw-medium">Moto</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- @extends('layouts.master')
@section('title', 'Profil Livreur')
@section('page-title', 'Mon Profil')
@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
                <div class="card-header bg-gradient-success p-0">
                    <div class="pt-5 pb-5 px-4 text-white text-center position-relative">
                        <div class="profile-avatar">
                            <img src="{{ asset('assets/images/default-livreur.png') }}" alt="Photo de profil" 
                                class="rounded-circle border-4 border-white shadow-sm" 
                                style="width: 150px; height: 150px; object-fit: cover; margin-top: 30px;">
                        </div>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="text-center mt-4 mb-4">
                        <h3 class="fw-bold mb-1">Amadou Diop</h3>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="me-2">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                            </div>
                            <span class="fw-semibold">4.8</span>
                            <span class="text-muted ms-1">(124 avis)</span>
                        </div>
                        <div class="mt-3">
                            <span class="badge rounded-pill bg-success px-3 py-2 fs-6">
                                <i class="fas fa-circle fs-7 me-1 align-middle"></i> Disponible
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 my-4">
                        <button class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-edit me-2"></i>Modifier le profil
                        </button>
                        <button class="btn btn-outline-danger px-4 py-2 rounded-pill">
                            <i class="fas fa-power-off me-2"></i>Mode Hors-ligne
                        </button>
                    </div>

                    <div class="mt-5">
                        <h5 class="border-bottom pb-2 mb-4 fw-bold text-success">Informations personnelles</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-3 me-3">
                                        <i class="fas fa-user text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 small">Nom complet</p>
                                        <p class="fw-semibold mb-0">Amadou Diop</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-3 me-3">
                                        <i class="fas fa-phone text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 small">Téléphone</p>
                                        <p class="fw-semibold mb-0">+221 77 123 45 67</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-3 me-3">
                                        <i class="fas fa-envelope text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 small">Email</p>
                                        <p class="fw-semibold mb-0">amadou.diop@colisfast.sn</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-3 me-3">
                                        <i class="fas fa-map-marker-alt text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 small">Adresse</p>
                                        <p class="fw-semibold mb-0">Parcelles Assainies, Unité 15, Dakar</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-3 me-3">
                                        <i class="fas fa-motorcycle text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 small">Type de véhicule</p>
                                        <p class="fw-semibold mb-0">Moto</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-3 me-3">
                                        <i class="fas fa-calendar-alt text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0 small">Membre depuis</p>
                                        <p class="fw-semibold mb-0">Juin 2023</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light py-4 mt-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="mb-0 fw-bold text-success">763</h4>
                            <p class="text-muted small mb-0">Livraisons</p>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0 fw-bold text-success">98%</h4>
                            <p class="text-muted small mb-0">À temps</p>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0 fw-bold text-success">35 200 FCFA</h4>
                            <p class="text-muted small mb-0">Gains hebdo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
    }
    
    .profile-avatar {
        margin-bottom: -75px;
    }
    
    .fs-7 {
        font-size: 0.75rem;
    }
    
    .border-4 {
        border-width: 4px !important;
    }
</style>
@endsection -->
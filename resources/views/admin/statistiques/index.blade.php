@extends('layouts.admin')

@section('title', 'Statistiques')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Livraisons Totales</h5>
                <p class="card-text">150</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Revenus Totaux</h5>
                <p class="card-text">1,500,000 FCFA</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Livreurs Actifs</h5>
                <p class="card-text">20</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Clients Inscrits</h5>
                <p class="card-text">300</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Détails des Livraisons</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID Livraison</th>
                        <th>Client</th>
                        <th>Adresse de Livraison</th>
                        <th>Statut</th>
                        <th>Date de Livraison</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>ndeya cisse</td>
                        <td>Mairie Parcelles, Dakar</td>
                        <td>Livrée</td>
                        <td>10/03/2025 14:30</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Moustapha</td>
                        <td>456 Avenue Principale, Dakar</td>
                        <td>En cours</td>
                        <td>11/03/2025 09:00</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                        </td>
                    </tr>
                    <!-- Ajoutez plus de lignes pour tester -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

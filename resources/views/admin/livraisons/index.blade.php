@extends('layouts.admin')

@section('title', 'Suivi des Livraisons')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Suivi des Livraisons</h5>
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
                        <td>Moustapha</td>
                        <td>123 Rue Principal, Dakar</td>
                        <td>En cours</td>
                        <td>10/03/2025 14:30</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                            <button class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Marquer comme Livré
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Ndeya Cisse</td>
                        <td>Mairie Parcelles, Dakar</td>
                        <td>Préparation</td>
                        <td>11/03/2025 09:00</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                            <button class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Marquer comme Livré
                            </button>
                        </td>
                    </tr>
                    <!-- Ajoutez plus de lignes pour tester -->
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            <!-- Pagination statique -->
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection

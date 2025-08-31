@extends('layouts.master')

@section('title', 'Mes revenus')
@section('page-title', 'Mes revenus')

@section('content')
<div class="container-fluid">
    
    <!-- ✅ Statistiques principales -->
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6 class="text-muted">Revenus du mois</h6>
                  <h3 class="text-primary fw-bold">{{ $revenuMois }} FCFA</h3>
                <i class="fas fa-calendar fa-2x text-primary"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6 class="text-muted">Revenus totaux</h6>
                 <h3 class="text-success fw-bold">{{ $revenuTotal }} FCFA</h3>
                <i class="fas fa-wallet fa-2x text-success"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6 class="text-muted">Livraisons ce mois</h6>
                <h3 class="text-info fw-bold">{{ $livraisonsMois }}</h3>
                <i class="fas fa-truck fa-2x text-info"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6 class="text-muted">Moyenne par livraison</h6>
                 <h3 class="text-warning fw-bold">{{ $moyenne }} FCFA</h3>
                <i class="fas fa-chart-line fa-2x text-warning"></i>
            </div>
        </div>
    </div>

    <!-- ✅ Graphique des revenus -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="fw-bold m-0 text-primary">Évolution des revenus (30 jours)</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenusChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- ✅ Répartition par type -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="fw-bold m-0 text-primary">Répartition des revenus</h6>
                </div>
                <div class="card-body">
                    <canvas id="typesChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Historique des paiements -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between">
                    <h6 class="fw-bold m-0 text-primary">Historique des paiements</h6>
                    <small class="text-muted">Dernières livraisons payées</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paiements as $paiement)
                                    <tr>
                                        <td>CMD-{{ $paiement->id }}</td>
                                        <td>{{ $paiement->updated_at->format('d/m/Y') }}</td>
                                        <td class="text-success fw-bold">
                                             +{{ intval($paiement->prix_final) }} FCFA
                                        </td>
                                        <td>
                                            @if($paiement->statut_paiement_livreur === 'Payée')
                                                <span class="badge bg-success">Payé</span>
                                            @else
                                                <span class="badge bg-warning">En attente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Aucun paiement trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    const revenusGraphUrl = "{{ route('livreur.api.revenus.graph', auth()->id()) }}";
    </script>
       <script src="{{ asset('js/revenus.js') }}"></script>


@endsection

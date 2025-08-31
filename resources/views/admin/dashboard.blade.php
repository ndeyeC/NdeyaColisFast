@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<!-- Div cachée pour stocker les données des graphiques -->
<div id="chart-data" 
     data-mois-labels="{{ json_encode($moisLabels) }}"
     data-livraison-data="{{ json_encode($livraisonsParMois) }}"
     data-revenus-data="{{ json_encode($revenusParMois) }}"
     data-statuts-labels="{{ json_encode($statutsLabels) }}"
     data-statuts-data="{{ json_encode(array_values($statuts)) }}"
     style="display: none;">
</div>

<div class="row">
    @include('admin.partials.dashboard-cards', [
        'totalLivreurs' => $totalLivreurs,
        'livreursEnAttente' => $livreursEnAttente,
        'totalLivraisons' => $totalLivraisons,
        'revenusTotaux' => $revenusTotaux,
    ])
    
    {{-- ✅ Actions rapides --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"> Actions rapides</h5>
                    <div>
                        <a href="{{ route('admin.trajets.urbains') }}" class="btn btn-primary">
                             Voir les trajets urbains & assigner des livraisons
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Graphiques -->
    <div class="col-md-8 mb-4">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">Livraisons par mois ({{ now()->year }})</h5>
            </div>
            <div class="card-body">
                <canvas id="livraisonsChart" height="250"></canvas>
            </div>
        </div>

        <div class="card mt-4 shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">Revenus mensuels (CFA)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenusChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Statuts des livraisons</h5>
            </div>
            <div class="card-body">
                <canvas id="statutsChart" height="250"></canvas>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top 5 livreurs</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($topLivreurs as $livreur)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $livreur->name }}
                            <span class="badge bg-success">{{ $livreur->livraisons_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
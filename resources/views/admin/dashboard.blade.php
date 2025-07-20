@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
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
                <h5 class="mb-0">🚀 Actions rapides</h5>
                <div>
                    <a href="{{ route('admin.trajets.urbains') }}" class="btn btn-primary">
                        🚗 Voir les trajets urbains & assigner des livraisons
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
<script>
    const moisLabels = @json($moisLabels);
    const livraisonData = @json($livraisonsParMois);
    const revenusData = @json($revenusParMois);
    const statutsLabels = @json($statutsLabels);
    const statutsData = @json(array_values($statuts));

    new Chart(document.getElementById('livraisonsChart'), {
        type: 'bar',
        data: {
            labels: moisLabels,
            datasets: [{
                label: 'Livraisons',
                data: livraisonData,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 5, precision: 0 }
                }
            }
        }
    });

    new Chart(document.getElementById('revenusChart'), {
        type: 'line',
        data: {
            labels: moisLabels,
            datasets: [{
                label: 'Revenus CFA',
                data: revenusData,
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.raw.toLocaleString() + ' CFA'
                    }
                }
            },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('statutsChart'), {
        type: 'doughnut',
        data: {
            labels: statutsLabels,
            datasets: [{
                data: statutsData,
                backgroundColor: [
                    '#0d6efd', '#ffc107', '#28a745',
                    '#dc3545', '#6c757d', '#6610f2'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
</script>
@endsection

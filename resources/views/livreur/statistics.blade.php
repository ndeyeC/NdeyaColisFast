@extends('layouts.master')

@section('title', 'Statistiques de performance')

@section('page-title', 'Statistiques de performance')

@section('content')
<div class="container-fluid">
    <!-- Cartes des statistiques principales -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Livraisons (Ce mois)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistiques['livraisons_mois'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Revenus (Ce mois)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistiques['revenus_mois'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taux de réussite</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $statistiques['taux_reussite'] }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{ $statistiques['taux_reussite'] }}%" 
                                            aria-valuenow="{{ $statistiques['taux_reussite'] }}" 
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Évaluation moyenne</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistiques['evaluation_moyenne'] }} / 5</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Graphique d'évolution des livraisons -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution des livraisons</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Période:</div>
                            <a class="dropdown-item active" href="#" data-periode="mois">Ce mois</a>
                            <a class="dropdown-item" href="#" data-periode="3mois">Les 3 derniers mois</a>
                            <a class="dropdown-item" href="#" data-periode="annee">Cette année</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="deliveriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tableau des dernières évaluations -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Derniers avis clients</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Commentaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluations as $evaluation)
                                <tr>
                                    <td>{{ $evaluation['client'] }}</td>
                                    <td>{{ $evaluation['date'] }}</td>
                                    <td>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $evaluation['note'])
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $evaluation['note'])
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($evaluation['commentaire'], 50) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucune évaluation disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte de performance et objectifs -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance et objectifs</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">Temps de livraison moyen 
                        <span class="float-right">{{ $performance['temps_livraison_moyen'] }} min</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ min(100, (30 - $performance['temps_livraison_moyen']) / 30 * 100) }}%" 
                            aria-valuenow="{{ $performance['temps_livraison_moyen'] }}"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <h4 class="small font-weight-bold">Livraisons complètes 
                        <span class="float-right">{{ $performance['pourcentage_livraisons_completes'] }}%</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" role="progressbar" 
                            style="width: {{ $performance['pourcentage_livraisons_completes'] }}%" 
                            aria-valuenow="{{ $performance['pourcentage_livraisons_completes'] }}"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <h4 class="small font-weight-bold">Évaluations positives 
                        <span class="float-right">{{ $performance['pourcentage_evaluations_positives'] }}%</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar" 
                            style="width: {{ $performance['pourcentage_evaluations_positives'] }}%" 
                            aria-valuenow="{{ $performance['pourcentage_evaluations_positives'] }}"
                            aria-valuemin="0" aria-valuemax="100"></div>
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
    // Données depuis le backend
    const evolutionData = @json($graphiques['evolution_livraisons']);
    const joursLabels = @json($graphiques['jours']);

    // Graphique d'évolution des livraisons
    var ctx = document.getElementById('deliveriesChart').getContext('2d');
    var deliveriesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: joursLabels,
            datasets: [{
                label: 'Livraisons',
                data: evolutionData,
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gestion du changement de période
    document.addEventListener('DOMContentLoaded', function() {
        const periodeLinks = document.querySelectorAll('[data-periode]');
        
        periodeLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const periode = this.dataset.periode;
                
                // Mise à jour visuelle
                periodeLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                // Appel AJAX pour récupérer les nouvelles données
                fetch(`/statistiques/periode?periode=${periode}`)
                    .then(response => response.json())
                    .then(data => {
                        // Mise à jour du graphique avec les nouvelles données
                        updateDeliveriesChart(data);
                    })
                    .catch(error => console.error('Erreur:', error));
            });
        });
    });

    function updateDeliveriesChart(data) {
        const labels = data.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('fr-FR', { 
                day: '2-digit', 
                month: '2-digit' 
            });
        });
        
        const values = data.map(item => item.total);
        
        deliveriesChart.data.labels = labels;
        deliveriesChart.data.datasets[0].data = values;
        deliveriesChart.update();
    }
</script>
@endsection
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($statistiques['livraisons_mois']) ? $statistiques['livraisons_mois'] : 0 }}
                            </div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($statistiques['revenus_mois']) ? number_format($statistiques['revenus_mois'], 0, ',', ' ') : 0 }} FCFA
                            </div>
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
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ isset($statistiques['taux_reussite']) ? $statistiques['taux_reussite'] : 0 }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{ isset($statistiques['taux_reussite']) ? $statistiques['taux_reussite'] : 0 }}%" 
                                            aria-valuenow="{{ isset($statistiques['taux_reussite']) ? $statistiques['taux_reussite'] : 0 }}" 
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($statistiques['evaluation_moyenne']) ? $statistiques['evaluation_moyenne'] : 0 }} / 5
                            </div>
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
        <div class="col-xl-8 col-lg-7">
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

        <!-- Graphique de répartition par quartier -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Livraisons par quartier</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="neighborhoodChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Plateau
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Ouakam
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Almadies
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Mermoz
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Autres
                        </span>
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
                                @if(isset($evaluations) && count($evaluations) > 0)
                                    @foreach($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ isset($evaluation['client']) ? $evaluation['client'] : 'N/A' }}</td>
                                        <td>{{ isset($evaluation['date']) ? $evaluation['date'] : 'N/A' }}</td>
                                        <td>
                                            <div class="text-warning">
                                                @php
                                                    $note = isset($evaluation['note']) ? $evaluation['note'] : 0;
                                                @endphp
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $note)
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i - 0.5 <= $note)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </td>
                                        <td>{{ isset($evaluation['commentaire']) ? Str::limit($evaluation['commentaire'], 50) : '' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Aucune évaluation disponible</td>
                                    </tr>
                                @endif
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
                        <span class="float-right">
                            {{ isset($performance['temps_livraison_moyen']) ? $performance['temps_livraison_moyen'] : 0 }} min
                        </span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ isset($performance['progress_temps_livraison']) ? $performance['progress_temps_livraison'] : 70 }}%" 
                            aria-valuenow="{{ isset($performance['temps_livraison_moyen']) ? $performance['temps_livraison_moyen'] : 0 }}"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <h4 class="small font-weight-bold">Livraisons complètes 
                        <span class="float-right">
                            {{ isset($performance['pourcentage_livraisons_completes']) ? $performance['pourcentage_livraisons_completes'] : 0 }}%
                        </span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" role="progressbar" 
                            style="width: {{ isset($performance['pourcentage_livraisons_completes']) ? $performance['pourcentage_livraisons_completes'] : 0 }}%" 
                            aria-valuenow="{{ isset($performance['pourcentage_livraisons_completes']) ? $performance['pourcentage_livraisons_completes'] : 0 }}"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <h4 class="small font-weight-bold">Évaluations positives 
                        <span class="float-right">
                            {{ isset($performance['pourcentage_evaluations_positives']) ? $performance['pourcentage_evaluations_positives'] : 0 }}%
                        </span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar" 
                            style="width: {{ isset($performance['pourcentage_evaluations_positives']) ? $performance['pourcentage_evaluations_positives'] : 0 }}%" 
                            aria-valuenow="{{ isset($performance['pourcentage_evaluations_positives']) ? $performance['pourcentage_evaluations_positives'] : 0 }}"
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
    // Données depuis le backend avec valeurs par défaut
    @php
        $defaultEvolution = [10, 20, 30, 25, 15, 35, 40];
        $defaultJours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $defaultQuartiers = ['Plateau', 'Ouakam', 'Almadies', 'Mermoz', 'Autres'];
        $defaultDonneesQuartiers = [20, 30, 25, 15, 10];
    @endphp
    
    const evolutionData = {!! json_encode(isset($graphiques['evolution_livraisons']) ? $graphiques['evolution_livraisons'] : $defaultEvolution) !!};
    const joursLabels = {!! json_encode(isset($graphiques['jours']) ? $graphiques['jours'] : $defaultJours) !!};
    const quartiersLabels = {!! json_encode(isset($graphiques['quartiers']) ? $graphiques['quartiers'] : $defaultQuartiers) !!};
    const quartiersData = {!! json_encode(isset($graphiques['donnees_quartiers']) ? $graphiques['donnees_quartiers'] : $defaultDonneesQuartiers) !!};

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

    // Graphique de répartition des livraisons par quartier
    var ctx2 = document.getElementById('neighborhoodChart').getContext('2d');
    var neighborhoodChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: quartiersLabels,
            datasets: [{
                data: quartiersData,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            }]
        },
        options: {
            responsive: true
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
                fetch('/statistiques/periode?periode=' + periode)
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
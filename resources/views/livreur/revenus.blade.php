@extends('layouts.master')

@section('title', 'Mes revenus')

@section('page-title', 'Mes revenus')

@section('content')
<div class="container-fluid">
    <!-- Résumé des revenus -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Revenus (Ce mois)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">35,800 FCFA</div>
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
                                Revenus (Total)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">187,500 FCFA</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Livraisons (Ce mois)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">42</div>
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
                                Moyenne par livraison</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">852 FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des revenus -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aperçu des revenus</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Période:</div>
                            <a class="dropdown-item active" href="#">Ce mois</a>
                            <a class="dropdown-item" href="#">Les 3 derniers mois</a>
                            <a class="dropdown-item" href="#">Cette année</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de répartition par type de livraison -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des revenus</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options:</div>
                            <a class="dropdown-item" href="#">Par type de colis</a>
                            <a class="dropdown-item" href="#">Par zone</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="earningsSourceChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Livraisons standard
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Livraisons express
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Livraisons volumineuses
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Détails des paiements -->
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des paiements</h6>
                    <div>
                        <select class="form-select form-select-sm" id="paymentMonthFilter">
                            <option selected>Mars 2025</option>
                            <option>Février 2025</option>
                            <option>Janvier 2025</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>CF-PAY-25031</td>
                                    <td>15/03/2025</td>
                                    <td>Paiement hebdomadaire</td>
                                    <td class="text-success">+8,500 FCFA</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                </tr>
                                <tr>
                                    <td>CF-PAY-25028</td>
                                    <td>08/03/2025</td>
                                    <td>Paiement hebdomadaire</td>
                                    <td class="text-success">+12,200 FCFA</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                </tr>
                                <tr>
                                    <td>CF-PAY-25024</td>
                                    <td>01/03/2025</td>
                                    <td>Paiement hebdomadaire</td>
                                    <td class="text-success">+9,300 FCFA</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                </tr>
                                <tr>
                                    <td>CF-PAY-25021</td>
                                    <td>22/02/2025</td>
                                    <td>Paiement hebdomadaire</td>
                                    <td class="text-success">+10,800 FCFA</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                </tr>
                                <tr>
                                    <td>CF-PAY-25018</td>
                                    <td>15/02/2025</td>
                                    <td>Paiement hebdomadaire</td>
                                    <td class="text-success">+11,500 FCFA</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Détails de portefeuille virtuel -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Mon portefeuille</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="text-success fw-bold">5,800 FCFA</h2>
                        <p class="text-muted">Solde actuel disponible</p>
                        
                        <button class="btn btn-primary">
                            <i class="fas fa-money-bill-wave me-2"></i>Demander un retrait
                        </button>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Options de paiement</h6>
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="orange" checked>
                            <label class="form-check-label" for="orange">
                                <i class="fas fa-mobile-alt text-warning me-1"></i> Orange Money
                            </label>
                        </div>
                        <div class="text-muted">*****7890</div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="wave">
                            <label class="form-check-label" for="wave">
                                <i class="fas fa-water text-info me-1"></i> Wave
                            </label>
                        </div>
                        <div class="text-muted">*****5432</div>
                    </div>
                    
                    <button class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-plus-circle me-1"></i> Ajouter une méthode de paiement
                    </button>
                </div>
            </div>
        </div>

        <!-- Performances et catégories -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance et bonus</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <div class="me-3">
                            <i class="fas fa-info-circle fa-2x text-info"></i>
                        </div>
                        <div>
                            <p class="mb-0">Vous êtes à 8 livraisons de débloquer un bonus de 5,000 FCFA ce mois-ci!</p>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Progression vers les bonus</h6>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Objectif 50 livraisons</span>
                            <span>42/50</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 84%" 
                                 aria-valuenow="84" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">Récompense: 5,000 FCFA</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Note client 4.8+</span>
                            <span>4.8/5.0</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 96%" 
                                 aria-valuenow="96" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">Récompense: 3,000 FCFA</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Livraisons express</span>
                            <span>12/20</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 60%" 
                                 aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">Récompense: 4,000 FCFA</small>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-trophy me-2"></i>Voir tous les défis
                        </button>
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
        // Simulation de données pour le graphique de revenus
        var ctx = document.getElementById("earningsChart");
        if (ctx) {
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ["1 Mar", "5 Mar", "10 Mar", "15 Mar", "20 Mar", "25 Mar", "30 Mar"],
                    datasets: [{
                        label: "Revenus",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: [0, 5000, 12000, 18000, 25000, 30000, 35800],
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value) {
                                    return value + ' FCFA';
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                return 'Revenus: ' + tooltipItem.yLabel + ' FCFA';
                            }
                        }
                    }
                }
            });
        }
                // Simulation de données pour le graphique de répartition des revenus
                var ctx2 = document.getElementById("earningsSourceChart");
        if (ctx2) {
            var myPieChart = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['Livraisons standard', 'Livraisons express', 'Livraisons volumineuses'],
                    datasets: [{
                        data: [50, 30, 20], // Example data: 50% standard, 30% express, 20% volumineous
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'], // Corresponding colors for each category
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ": " + tooltipItem.raw + "%";
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

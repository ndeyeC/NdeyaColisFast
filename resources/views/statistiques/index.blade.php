@extends('layouts.master')

@section('title', 'Statistiques de performance')
@section('page-title', 'Statistiques de performance')

@section('content')
<div class="container-fluid">
    <!-- Cartes des statistiques principales -->
    <div class="row">
        <!-- Livraisons ce mois -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Livraisons (Ce mois)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['livraisons_mois'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus ce mois -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Revenus (Ce mois)
                            </div>
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

        <!-- Taux de réussite -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux de réussite
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['taux_reussite'] ?? 0 }}%
                            </div>
                            <div class="progress progress-sm mr-2">
                                <div class="progress-bar bg-info" role="progressbar"
                                     style="width: {{ $statistiques['taux_reussite'] ?? 0 }}%"
                                     aria-valuenow="{{ $statistiques['taux_reussite'] ?? 0 }}"
                                     aria-valuemin="0" aria-valuemax="100">
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

        <!-- Évaluation moyenne -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Évaluation moyenne
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['evaluation_moyenne'] ?? 0 }} / 5
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

    <!-- Derniers avis clients -->
    <div class="row">
        <div class="col-lg-12 mb-4">
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
                                @forelse($evaluations ?? [] as $evaluation)
                                <tr>
                                    <td>{{ $evaluation['client'] ?? 'N/A' }}</td>
                                    <td>{{ $evaluation['date'] ?? 'N/A' }}</td>
                                    <td>
                                        <div class="text-warning">
                                            @php $note = $evaluation['note'] ?? 0; @endphp
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
                                    <td>{{ isset($evaluation['commentaire']) ? \Illuminate\Support\Str::limit($evaluation['commentaire'], 50) : '' }}</td>
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
    </div>
</div>
@endsection

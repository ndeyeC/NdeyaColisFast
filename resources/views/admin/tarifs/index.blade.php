@extends('layouts.admin')

@section('title', 'Gestion des Tarifs')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Liste des Tarifs de Livraison</h5>
            <a href="{{ route('admin.tarifs.create') }}" class="btn btn-danger fw-bold">
                <i class="fas fa-plus"></i> Ajouter un Tarif
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-md-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
            </div>
            <div class="col-md-3">
                <select id="zoneFilter" class="form-select">
                    <option value="">Toutes les zones</option>
                    @foreach($tarifs->pluck('zone')->unique() as $zone)
                        <option value="{{ $zone }}">{{ $zone }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="typeFilter" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach($tarifs->pluck('type_livraison')->unique() as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="zoneTypeFilter" class="form-select">
                    <option value="">Tous les types de zone</option>
                    @foreach($tarifs->pluck('type_zone')->unique() as $zoneType)
                        <option value="{{ $zoneType }}">{{ ucfirst(str_replace('_', ' ', $zoneType)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tableau des tarifs -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Zone</th>
                        <th>Type</th>
                        <th>Type de zone</th>
                        <th>Distance</th>
                        <th>Poids</th>
                        <th>Prix (FCFA)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifs as $tarif)
                    <tr class="tarif-row" 
                        data-zone="{{ $tarif->zone }}"
                        data-type="{{ $tarif->type_livraison }}"
                        data-typezone="{{ $tarif->type_zone }}">
                        <td>{{ $tarif->zone }}</td>
                        <td>{{ $tarif->type_livraison }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $tarif->type_zone)) }}</td>
                        <td>{{ $tarif->tranche_distance }}</td>
                        <td>{{ $tarif->tranche_poids }}</td>
                        <td>{{ number_format($tarif->prix, 0, '', ' ') }}</td>
                        <td>
                            <a href="{{ route('admin.tarifs.edit', $tarif->id) }}" 
                               class="btn btn-sm btn-outline-danger" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.tarifs.destroy', $tarif->id) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce tarif?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Aucun tarif trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $tarifs->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')

    <script src="{{ asset('js/tarif.js') }}"></script>

@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/tarif.css') }}">

@endsection

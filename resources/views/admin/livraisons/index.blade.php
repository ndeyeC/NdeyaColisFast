@extends('layouts.admin')

@section('title', 'Suivi des Livraisons')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['total_livraisons'] }}</h4>
                        <span>Total Livraisons</span>
                    </div>
                    <i class="fas fa-truck fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['en_cours'] }}</h4>
                        <span>En Cours</span>
                    </div>
                    <i class="fas fa-clock fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['livrees'] }}</h4>
                        <span>Livrées</span>
                    </div>
                    <i class="fas fa-check fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['problemes_signales'] }}</h4>
                        <span>Problèmes Signalés</span>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Suivi des Livraisons</h5>
        <div class="btn-group">
            <a href="{{ route('admin.livraisons.problemes') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-exclamation-triangle"></i> Problèmes Signalés ({{ $stats['problemes_signales'] }})
            </a>
            
        </div>
    </div>
    <div class="card-body">
        <!-- Filtres -->
        <form id="filtres-form" class="row g-3 mb-4">
            <!-- ... (contenu existant des filtres) ... -->
        </form>

        <div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Livreur</th>
                <th>Adresse de Livraison</th>
                <th>Statut</th>
                <th>Problème</th>
                <th>Prix</th>
                <th>Date de Création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($livraisons as $livraison)
            <tr class="{{ $livraison->probleme_signale ? 'table-warning' : '' }}">
                <td><strong>#{{ $livraison->id }}</strong></td>
                <td>
                    <div>{{ $livraison->user->name ?? 'N/A' }}</div>
                    <small class="text-muted">{{ $livraison->user->email ?? '' }}</small>
                </td>
                <td>
                    @if($livraison->driver)
                        <span class="badge bg-info">{{ $livraison->driver->name }}</span>
                    @else
                        <span class="badge bg-secondary">Non assigné</span>
                    @endif
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 200px;" title="{{ $livraison->adresse_arrivee }}">
                        {{ $livraison->adresse_arrivee }}
                    </div>
                    @if($livraison->details_adresse_arrivee)
                        <small class="text-muted">{{ $livraison->details_adresse_arrivee }}</small>
                    @endif
                </td>
                <td>
                    @php
                        $statusColors = [
                            'en_attente_paiement' => 'secondary',
                            'payee' => 'info',
                            'confirmee' => 'primary',
                            'acceptee' => 'warning',
                            'en_cours' => 'primary',
                            'livree' => 'success',
                            'probleme_signale' => 'danger',
                            'annulee' => 'dark'
                        ];
                        $statusLabels = [
                            'en_attente_paiement' => 'En attente',
                            'payee' => 'Payée',
                            'confirmee' => 'Confirmée',
                            'acceptee' => 'Acceptée',
                            'en_cours' => 'En cours',
                            'livree' => 'Livrée',
                            'probleme_signale' => 'Problème',
                            'annulee' => 'Annulée'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$livraison->status] ?? 'secondary' }}">
                        {{ $statusLabels[$livraison->status] ?? $livraison->status }}
                    </span>
                </td>
                <td>
                    @if($livraison->probleme_signale)
                        @php
                            $probleme = is_array($livraison->probleme_signale) 
                                ? $livraison->probleme_signale 
                                : json_decode($livraison->probleme_signale, true);
                        @endphp
                        
                        @if(is_array($probleme) && !empty($probleme))
                            <span class="badge bg-danger" title="{{ $probleme['description'] ?? 'Description non disponible' }}">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ isset($probleme['type_probleme']) ? ucfirst(str_replace('_', ' ', $probleme['type_probleme'])) : 'Problème' }}
                            </span>
                            @if(isset($probleme['status']))
                                <small class="d-block {{ $probleme['status'] === 'en_attente' ? 'text-danger' : 'text-success' }}">
                                    {{ $probleme['status'] === 'en_attente' ? 'À traiter' : 'Traité' }}
                                </small>
                            @endif
                        @else
                            <span class="text-muted">Données invalides</span>
                        @endif
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td><strong>{{ number_format($livraison->prix_final, 0, ',', ' ') }} FCFA</strong></td>
                <td>
                    <div>{{ $livraison->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $livraison->created_at->format('H:i') }}</small>
                </td>
                <td>
                    <a href="{{ route('admin.livraisons.show', $livraison->id) }}" class="btn btn-info btn-sm" title="Voir détails">
                        <i class="fas fa-eye"></i> Détails
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune livraison trouvée</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $livraisons->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal pour traiter les problèmes -->
<div class="modal fade" id="modalProbleme" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Traiter le Problème Signalé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProbleme" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="detailsProbleme"></div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Action à effectuer *</label>
                        <select class="form-select" name="action" required>
                            <option value="">Choisir une action</option>
                            <option value="resolu">Marquer comme résolu (continuer la livraison)</option>
                            <option value="reassigner">Réassigner à un autre livreur</option>
                            <option value="annuler">Annuler la commande</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="divNouveauLivreur" style="display: none;">
                        <label class="form-label">Nouveau livreur</label>
                        <select class="form-select" name="nouveau_driver_id">
                            <option value="">Choisir un livreur</option>
                            @foreach($livreurs as $livreur)
                                <option value="{{ $livreur->id }}">{{ $livreur->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Commentaire administrateur *</label>
                        <textarea class="form-control" name="commentaire_admin" rows="3" required placeholder="Expliquer l'action prise..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Traiter le Problème</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/livraison.js') }}"></script>

@endpush
@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Détail de la livraison #{{ $livraison->id }}</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Informations client</h5>
            <p><strong>Nom :</strong> {{ $livraison->user->name ?? 'Non disponible' }}</p>
            <p><strong>Email :</strong> {{ $livraison->user->email ?? 'Non disponible' }}</p>

            <hr>

            <h5>Informations livraison</h5>
            <p><strong>Adresse de départ :</strong> {{ $livraison->adresse_depart }}</p>
            <p><strong>Adresse d'arrivée :</strong> {{ $livraison->adresse_arrivee }}</p>
            <p><strong>Statut :</strong> <span class="badge bg-{{ $livraison->status === 'livre' ? 'success' : ($livraison->status === 'probleme_signale' ? 'danger' : 'warning') }}">
                {{ ucfirst(str_replace('_', ' ', $livraison->status)) }}
            </span></p>
            <p><strong>Prix :</strong> {{ number_format($livraison->prix_final, 2) }} FCFA</p>
            <p><strong>Type de livraison :</strong> {{ $livraison->type_livraison }}</p>
            <p><strong>Mode de paiement :</strong> {{ $livraison->mode_paiement }}</p>

            <hr>

            <h5>Livreur assigné</h5>
            @if($livraison->driver)
                <p>{{ $livraison->driver->name }} ({{ $livraison->driver->phone ?? 'Tél. non disponible' }})</p>
            @else
                <p class="text-muted"><em>Aucun livreur assigné</em></p>
            @endif

            @if(!empty($problemeSignale) && is_array($problemeSignale))
                <hr>
                <div class="alert alert-danger">
                    <h5 class="alert-heading">⚠ Problème signalé</h5>
                    
                    <div class="mb-2">
                        <strong>Type :</strong> 
                        <span class="badge bg-dark">
                            {{ $problemeSignale['type'] ?? 'Non spécifié' }}
                        </span>
                    </div>

                    <div class="mb-2">
                        <strong>Message :</strong>
                        <div class="alert alert-light p-2 mt-1">
                            {{ $problemeSignale['message'] ?? 'Aucun détail fourni' }}
                        </div>
                    </div>

                    @if(!empty($problemeSignale['date_signalement']))
                        <div class="mb-2">
                            <strong>Date :</strong>
                            {{ \Carbon\Carbon::parse($problemeSignale['date_signalement'])->translatedFormat('d/m/Y \à H\hi') }}
                        </div>
                    @else
                        <div class="text-muted">
                            <small>Date de signalement non disponible</small>
                        </div>
                    @endif

                    @if(!empty($problemeSignale['status']))
                        <div class="mt-2">
                            <strong>Statut :</strong>
                            <span class="badge bg-{{ $problemeSignale['status'] === 'resolu' ? 'success' : 'warning' }}">
                                {{ ucfirst($problemeSignale['status']) }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.livraisons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        
        @if($livraison->status === 'probleme_signale')
            <a href="{{ route('admin.livraisons.resolve', $livraison->id) }}" class="btn btn-success">
                <i class="fas fa-check"></i> Marquer comme résolu
            </a>
        @endif
    </div>
</div>
@endsection
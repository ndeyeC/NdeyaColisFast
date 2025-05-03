@extends('layouts.admin')

@section('title', "Détails du livreur : {$livreur->name}")

@section('content')
<div class="card">
    <div class="card-body">
        <h3>{{ $livreur->name }}</h3>
        <p><strong>Email :</strong> {{ $livreur->email }}</p>
        <p><strong>Téléphone :</strong> {{ $livreur->numero_telephone }}</p>
        <p><strong>Type de véhicule :</strong> {{ $livreur->vehicule ?? 'Non spécifié' }}</p>
        <a href="{{ route('admin.livreurs.index') }}" class="btn btn-sm btn-secondary">Retour</a>
    </div>
</div>
@endsection

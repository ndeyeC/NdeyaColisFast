@extends('layouts.template')

@section('title', 'Mes Commandes')

@section('content')
<div class="max-w-3xl mx-auto mt-8">
    <h2 class="text-xl font-semibold mb-6">Mes Commandes</h2>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($commnandes as $commnande)
        <div class="mb-4 p-4 border rounded shadow-sm">
            <p><strong>Départ :</strong> {{ $commnande->adresse_depart }}</p>
            <p><strong>Destination :</strong> {{ $commnande->adresse_arrivee }}</p>
            <p><strong>Type de livraison :</strong> {{ ucfirst($commnande->type_livraison) }}</p>
            <p><strong>Prix :</strong> {{ number_format($commnande->prix, 0, ',', ' ') }} FCFA</p>
        </div>
    @empty
        <p class="text-gray-600">Aucune commande pour le moment.</p>
    @endforelse
</div>
@endsection

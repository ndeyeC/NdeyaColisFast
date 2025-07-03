@extends('layouts.template')

@section('title', 'Détails de la commande')

@section('content')
<div class="max-w-3xl mx-auto mt-6 px-4">
    <div class="flex items-center mb-6">
        <a href="{{ route('commnandes.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Retour à l'historique
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Détails de la commande #{{ $commnande->id }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Adresse de départ</h3>
                <p class="text-gray-600">{{ $commnande->adresse_depart }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Adresse d'arrivée</h3>
                <p class="text-gray-600">{{ $commnande->adresse_arrivee }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Date</h3>
                <p class="text-gray-600">{{ $commnande->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Statut</h3>
                <span class="text-xs font-semibold px-2 py-1 rounded-full 
                    @if($commande->statut == 'LIVRÉE') bg-green-100 text-green-800
                    @elseif($commande->statut == 'ANNULÉE') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ strtoupper($commnande->statut) }}
                </span>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Montant</h3>
                <p class="text-gray-600">{{ number_format($commnande->prix_final, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 mb-2">Livreur</h3>
            <p class="text-gray-600">{{ $commnande->livreur->nom  }}</p>
            @if(isset($commnande->livreur->rating))
                <div class="flex items-center mt-1">
                    <span class="text-yellow-500">★</span>
                    <span class="ml-1 text-gray-600">{{ $commnande->livreur->rating }}/5</span>
                </div>
            @endif
        </div>

        <!-- Ajoutez ici d'autres détails si nécessaire -->
    </div>
</div>
@endsection
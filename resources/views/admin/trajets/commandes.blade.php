@extends('layouts.master')

@section('title', 'Commandes du trajet')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">
        📦 Commandes pour le trajet vers {{ $trajet->destination_region }}
    </h2>

    @if($commandes->isEmpty())
        <div class="p-5 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-xl text-center font-medium shadow-sm">
            ❌ Aucune commande assignée à ce trajet.
        </div>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach($commandes as $commande)
                <li class="py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold">Client : {{ $commande->user->name ?? 'Inconnu' }}</p>
                            <p>Adresse : {{ $commande->adresse_livraison ?? 'Non précisée' }}</p>
                            <p>Status : {{ $commande->statut }}</p>
                        </div>
                        <div>
                            <span class="inline-block px-3 py-1 bg-blue-600 text-white text-sm rounded-lg">
                                📦 #{{ $commande->id }}
                            </span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="mt-6 text-center">
        <a href="{{ route('livreur.dashboarde') }}"
           class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-xl shadow-md transition">
           ⬅️ Retour au tableau de bord
        </a>
    </div>
</div>
@endsection

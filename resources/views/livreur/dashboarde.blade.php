@extends('layouts.master')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Revenus aujourd'hui -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 mr-4">
                <i class="fas fa-money-bill text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Revenus aujourd'hui</p>
                <p class="text-xl font-bold text-gray-700">{{ number_format($statistiques['revenus_jour']) }} FCFA</p>
                <p class="text-xs text-red-500">
                    <i class="fas fa-arrow-up mr-1"></i>
                    @php
                        $pourcentage = $statistiques['revenus_jour'] > 0 ? '+15%' : '0%';
                    @endphp
                    {{ $pourcentage }} par rapport à hier
                </p>
            </div>
        </div>
    </div>

    <!-- Livraisons complétées -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 mr-4">
                <i class="fas fa-check-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Livraisons complétées</p>
                <p class="text-xl font-bold text-gray-700">
                    {{ $statistiques['livraisons_completees'] ?? 0 }} / {{ $statistiques['livraisons_total'] ?? 0 }}
                </p>
                <p class="text-xs text-red-500">
                    <i class="fas fa-arrow-up mr-1"></i> 
                    {{ $statistiques['taux_completion'] ?? 0 }}% taux de complétion
                </p>
            </div>
        </div>
    </div>

    <!-- Note moyenne -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 mr-4">
                <i class="fas fa-star text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Note moyenne</p>
                <p class="text-xl font-bold text-gray-700">{{ $statistiques['note_moyenne'] ?? 0 }} / 5</p>
                <div class="flex text-red-500 text-xs">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($statistiques['note_moyenne'] ?? 0))
                            <i class="fas fa-star"></i>
                        @elseif($i == ceil($statistiques['note_moyenne'] ?? 0) && ($statistiques['note_moyenne'] ?? 0) > floor($statistiques['note_moyenne'] ?? 0))
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Distance parcourue -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 mr-4">
                <i class="fas fa-route text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Distance parcourue</p>
                <p class="text-xl font-bold text-gray-700">{{ $statistiques['distance_jour'] ?? 0 }} km</p>
                <p class="text-xs text-gray-500">aujourd'hui</p>
            </div>
        </div>
    </div>
</div>


<!-- Livraison en cours -->
@if($livraisonActuelle)
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Livraison en cours</h2>
    </div>
    <div class="p-4">
        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <div class="bg-red-100 p-3 rounded-lg">
                <i class="fas fa-box text-red-600 text-2xl"></i>
            </div>

            <div class="flex-1">
                <h3 class="font-medium text-gray-900">Commnande #{{ $livraisonActuelle->reference }}</h3>
                <div class="flex flex-col sm:flex-row sm:items-center text-sm text-gray-600 mt-1">
                    <span class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> {{ $livraisonActuelle->adresse_depart }}
                    </span>
                    <span class="hidden sm:inline mx-2">•</span>
                    <span>15 min</span>
                </div>
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        En route
                    </span>
                </div>
            </div>

            <div class="flex space-x-2">
                <!-- ✅ Route corrigée ici -->
                <a href="{{ route('livreur.livraison-cours') }}" 
                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-truck-moving mr-2"></i>
                    Voir la livraison
                </a>
            </div>
        </div>

        @php
            $progress = $statistiques['taux_completion'] ?? 0;
        @endphp
        <div class="mt-4">
            <div class="flex justify-between mb-1 text-xs text-gray-600">
                <span>En route vers le client</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Livraisons disponibles -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Livraisons disponibles</h2>
        <a href="{{ route('livreur.livraisons-disponible') }}" class="text-sm text-red-600 hover:text-red-700">Voir tout</a>
    </div>
    <div class="p-4">
        <div class="space-y-4">
            @forelse($livraisonsDisponibles as $livraison)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-medium text-gray-900">Commnande #{{ $livraison->reference }}</h3>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> 
                                {{ $livraison->adresse_depart }}
                            </span>
                            
                        </div>
                        <div class="mt-2 flex space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $livraison->type_colis }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ number_format($livraison->prix_final) }} FCFA
                            </span>
                        </div>
                    </div>

                    <button onclick="accepterCommande({{ $livraison->id }})" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-sm">
                        Accepter
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Aucune livraison disponible actuellement</p>
            </div>
            @endforelse
        </div>
    </div>
</div>



@endsection

@section('scripts')
<script>
function accepterCommande(commandeId) {
    fetch(`/livreur/commandes/${commandeId}/accepter`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de l\'acceptation');
        }
    });
}
</script>
@endsection

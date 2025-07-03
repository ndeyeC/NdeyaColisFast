@extends('layouts.template')

@section('title', 'Mes Commandes')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <!-- Header avec bouton retour -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ url()->previous() }}" 
                   class="flex items-center justify-center w-10 h-10 bg-white rounded-full shadow-md hover:shadow-lg transition-all duration-200 text-gray-600 hover:text-blue-600 group">
                    <i class="fas fa-arrow-left text-lg group-hover:transform group-hover:-translate-x-0.5 transition-transform"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mes Livraisons</h1>
                    <p class="text-gray-600 mt-1">Historique de toutes vos commandes</p>
                </div>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="hidden sm:flex items-center space-x-4 text-sm">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                    <span class="text-gray-500">Total : </span>
                    <span class="font-semibold text-gray-900">{{ $commnandes->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Message de succès -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4 rounded-xl mb-6 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-xl mr-3"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Liste des commandes -->
        <div class="space-y-4">
            @forelse ($commnandes as $commande)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 overflow-hidden group">
                    
                    <!-- En-tête de la commande -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shipping-fast text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Commande #{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-gray-500">{{ $commande->created_at->format('d M Y à H:i') }}</p>
                                </div>
                            </div>
                            
                            <!-- Badge statut -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                @if($commande->statut == 'LIVRÉE') 
                                    bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-200
                                @elseif($commande->statut == 'ANNULÉE') 
                                    bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-200
                                @else 
                                    bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-200
                                @endif">
                                @if($commande->statut == 'LIVRÉE')
                                    <i class="fas fa-check-circle mr-1"></i>
                                @elseif($commande->statut == 'ANNULÉE')
                                    <i class="fas fa-times-circle mr-1"></i>
                                @else
                                    <i class="fas fa-clock mr-1"></i>
                                @endif
                                {{ strtoupper($commande->statut) }}
                            </span>
                        </div>
                    </div>

                    <!-- Contenu de la commande -->
                    <div class="p-6">
                        
                        <!-- Itinéraire -->
                        <div class="mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <div class="w-0.5 h-8 bg-gray-300 mx-auto my-1"></div>
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="flex-1 space-y-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                                        <span class="text-gray-700 font-medium">{{ $commande->adresse_depart }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-flag-checkered text-green-500 mr-2"></i>
                                        <span class="text-gray-700 font-medium">{{ $commande->adresse_arrivee }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Détails de la commande -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-box text-gray-600"></i>
                                    <span class="text-sm font-medium text-gray-600">Type de livraison</span>
                                </div>
                                <p class="text-lg font-semibold text-gray-900 capitalize">{{ $commande->type_livraison }}</p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-money-bill-wave text-gray-600"></i>
                                    <span class="text-sm font-medium text-gray-600">Montant</span>
                                </div>
                                <p class="text-lg font-bold text-blue-600">{{ number_format($commande->prix_final, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                @if($commande->livreur)
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-1"></i>
                                        Livreur: {{ $commande->livreur->name }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @if($commande->statut == 'LIVRÉE')
                                    <button class="flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-redo mr-2"></i>
                                        Recommander
                                    </button>
                                @endif
                                
                                <button class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>
                                    Détails
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- État vide amélioré -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shipping-fast text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune livraison</h3>
                    <p class="text-gray-500 mb-6">Vous n'avez pas encore effectué de commande.</p>
                    <a href="{{ route('livraison.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>
                        Faire une livraison
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>

<!-- Styles additionnels -->
<style>
    .group:hover .group-hover\:transform {
        transform: translateX(-2px);
    }
    
    @media (max-width: 640px) {
        .grid-cols-1.md\:grid-cols-3 {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
    }
</style>
@endsection
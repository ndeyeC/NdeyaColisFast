@extends('layouts.master')

@section('title', 'Livraisons en cours')

@section('page-title', 'Livraisons en cours')

@section('content')
<!-- Current Delivery Card -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Livraison actuelle</h2>
    </div>
    <div class="p-4">
        <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0">
            <div class="w-full md:w-2/3 pr-0 md:pr-4">
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg mr-3">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">Commande #2587</h3>
                            <p class="text-sm text-gray-600">Restaurant Le Plateau - Burger & Frites</p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                En route
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col space-y-3">
                        <div class="flex items-start">
                            <div class="flex items-center justify-center bg-green-100 rounded-full p-2 mr-3">
                                <i class="fas fa-store text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Récupération</h4>
                                <p class="text-sm text-gray-600">Plateau, Rue 14, Dakar</p>
                                <p class="text-xs text-gray-500">Restaurant Le Plateau - 2ème étage</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center justify-center bg-red-100 rounded-full p-2 mr-3">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Livraison</h4>
                                <p class="text-sm text-gray-600">Plateau, Avenue Léopold Sédar Senghor, Dakar</p>
                                <p class="text-xs text-gray-500">Immeuble Azur, 5ème étage, Bureau 502</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="flex justify-between mb-1 text-xs text-gray-600">
                            <span>En route vers le client</span>
                            <span>65%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col space-y-2 mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Distance restante:</span>
                            <span class="font-medium">1.2 km</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Temps estimé:</span>
                            <span class="font-medium">8 minutes</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Heure d'arrivée prévue:</span>
                            <span class="font-medium">14:35</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <button class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg shadow-sm flex items-center justify-center">
                        <i class="fas fa-navigation mr-2"></i>
                        Ouvrir la navigation
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg shadow-sm flex items-center justify-center">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-3 rounded-lg shadow-sm flex items-center justify-center">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            
            <div class="w-full md:w-1/3">
                <div class="h-64 bg-gray-100 rounded-lg relative">
                    <!-- This would be replaced with an actual map in the final implementation -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <p class="text-gray-500">Carte de l'itinéraire</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm mt-4 p-4 border border-gray-200">
                    <h3 class="font-medium text-gray-900 mb-2">Actions</h3>
                    <div class="space-y-2">
                        <button class="w-full text-left bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded flex items-center">
                            <i class="fas fa-clipboard-check text-green-600 mr-2"></i>
                            Marquer comme livré
                        </button>
                        <button class="w-full text-left bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            Signaler un problème
                        </button>
                        <button class="w-full text-left bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded flex items-center">
                            <i class="fas fa-ban text-red-600 mr-2"></i>
                            Annuler la livraison
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Queue -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Livraisons en attente</h2>
    </div>
    <div class="p-4">
        <div class="space-y-4">
            <!-- Queued Delivery 1 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="flex items-start">
                        <div class="bg-purple-100 p-3 rounded-lg mr-3">
                            <i class="fas fa-shopping-bag text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">Commande #2592</h3>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <span class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Supermarché Casino
                                </span>
                                <span class="mx-2">•</span>
                                <span>Livraison à Ouakam</span>
                            </div>
                            <div class="mt-2 flex space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    En attente
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    1,500 FCFA
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <i class="fas fa-eye mr-2"></i>
                            Détails
                        </button>
                        <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Queued Delivery 2 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="flex items-start">
                        <div class="bg-pink-100 p-3 rounded-lg mr-3">
                            <i class="fas fa-pills text-pink-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">Commande #2595</h3>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <span class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Pharmacie de Mermoz
                                </span>
                                <span class="mx-2">•</span>
                                <span>Livraison à Sacré-Cœur</span>
                            </div>
                            <div class="mt-2 flex space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    En attente
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    1,800 FCFA
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <i class="fas fa-eye mr-2"></i>
                            Détails
                        </button>
                        <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
    <i class="fas fa-times mr-2"></i> Annuler
</button>
</div>
</div>
<!-- Queued Delivery 3 -->
<div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex items-start">
            <div class="bg-yellow-100 p-3 rounded-lg mr-3">
                <i class="fas fa-coffee text-yellow-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">Commande #2600</h3>
                <div class="flex items-center text-sm text-gray-600 mt-1">
                    <span class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Café Le Moka
                    </span>
                    <span class="mx-2">•</span>
                    <span>Livraison à Yoff</span>
                </div>
                <div class="mt-2 flex space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"> En attente </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"> 2,000 FCFA </span>
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm flex items-center">
                <i class="fas fa-eye mr-2"></i> Détails
            </button>
            <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                <i class="fas fa-times mr-2"></i> Annuler
            </button>
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection
                        
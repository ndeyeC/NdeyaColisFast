@extends('layouts.master')

@section('title', 'Tableau de bord')

@section('page-title', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Today's Earnings Card -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 mr-4">
                <i class="fas fa-money-bill text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Revenus aujourd'hui</p>
                <p class="text-xl font-bold text-gray-700">5,500 FCFA</p>
                <p class="text-xs text-green-500">
                    <i class="fas fa-arrow-up mr-1"></i> +15% par rapport à hier
                </p>
            </div>
        </div>
    </div>
    
    <!-- Completed Deliveries Card -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 mr-4">
                <i class="fas fa-check-circle text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Livraisons complétées</p>
                <p class="text-xl font-bold text-gray-700">8 / 10</p>
                <p class="text-xs text-blue-500">
                    <i class="fas fa-arrow-up mr-1"></i> 80% taux de complétion
                </p>
            </div>
        </div>
    </div>
    
    <!-- Average Rating Card -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 mr-4">
                <i class="fas fa-star text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Note moyenne</p>
                <p class="text-xl font-bold text-gray-700">4.8 / 5</p>
                <div class="flex text-yellow-500 text-xs">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Distance Card -->
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 mr-4">
                <i class="fas fa-route text-purple-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Distance parcourue</p>
                <p class="text-xl font-bold text-gray-700">32 km</p>
                <p class="text-xs text-gray-500">aujourd'hui</p>
            </div>
        </div>
    </div>
</div>

<!-- Current Status Section -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Statut actuel</h2>
    </div>
    <div class="p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span class="text-green-600 font-medium">En ligne</span>
            </div>
            
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer" checked>
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            </label>
        </div>
    </div>
</div>

<!-- Current or Next Delivery -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Livraison en cours</h2>
    </div>
    <div class="p-4">
        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-box text-blue-600 text-2xl"></i>
            </div>
            
            <div class="flex-1">
                <h3 class="font-medium text-gray-900">Commande #2587</h3>
                <div class="flex flex-col sm:flex-row sm:items-center text-sm text-gray-600 mt-1">
                    <span class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Plateau, Dakar
                    </span>
                    <span class="hidden sm:inline mx-2">•</span>
                    <span>2.5 km</span>
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
                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-navigation mr-2"></i>
                    Navigation
                </button>
                <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg shadow-sm flex items-center">
                    <i class="fas fa-phone"></i>
                </button>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="flex justify-between mb-1 text-xs text-gray-600">
                <span>En route vers le client</span>
                <span>65%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Available Deliveries -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Livraisons disponibles</h2>
        <a href="{{ url('/livreur/available-deliveries') }}" class="text-sm text-green-600 hover:text-green-700">Voir tout</a>
    </div>
    <div class="p-4">
        <div class="space-y-4">
            <!-- Delivery Item 1 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-medium text-gray-900">Commande #2593</h3>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Almadies, Dakar
                            </span>
                            <span class="mx-2">•</span>
                            <span>3.8 km</span>
                        </div>
                        <div class="mt-2 flex space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Restaurant
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                1,200 FCFA
                            </span>
                        </div>
                    </div>
                    
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                        Accepter
                    </button>
                </div>
            </div>
            
            <!-- Delivery Item 2 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-medium text-gray-900">Commande #2598</h3>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Mermoz, Dakar
                            </span>
                            <span class="mx-2">•</span>
                            <span>5.2 km</span>
                        </div>
                        <div class="mt-2 flex space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Supermarché
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                1,500 FCFA
                            </span>
                        </div>
                    </div>
                    
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                        Accepter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Deliveries -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Livraisons récentes</h2>
        <a href="#" class="text-sm text-green-600 hover:text-green-700">Historique complet</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider"># Commande</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#2563</td>
                    <td class="px-6 py-4 text-sm text-gray-500">1,200 FCFA</td>
                    <td class="px-6 py-4 text-sm text-green-500">Complétée</td>
                    <td class="px-6 py-4 text-sm">
                        <button class="text-blue-600 hover:text-blue-800">Voir</button>
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#2564</td>
                    <td class="px-6 py-4 text-sm text-gray-500">1,500 FCFA</td>
                    <td class="px-6 py-4 text-sm text-yellow-500">En attente</td>
                    <td class="px-6 py-4 text-sm">
                        <button class="text-blue-600 hover:text-blue-800">Voir</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

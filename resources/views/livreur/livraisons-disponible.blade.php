@extends('layouts.master')

@section('title', 'Livraisons disponibles')

@section('page-title', 'Livraisons disponibles')

@section('content')
<!-- Filters Section -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-10 p-2.5" placeholder="Rechercher une adresse...">
                </div>
                
                <div class="inline-block">
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                        <option selected>Distance</option>
                        <option value="1">< 2 km</option>
                        <option value="2">2-5 km</option>
                        <option value="3">5-10 km</option>
                        <option value="4">> 10 km</option>
                    </select>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center">
                    <i class="fas fa-filter mr-2"></i> Filtres
                </button>
                <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center">
                    <i class="fas fa-sort mr-2"></i> Trier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Carte des livraisons</h2>
    </div>
    <div class="h-64 bg-gray-100 relative">
        <!-- This would be replaced with an actual map in the final implementation -->
        <div class="absolute inset-0 flex items-center justify-center">
            <p class="text-gray-500">Carte des livraisons disponibles</p>
        </div>
    </div>
</div>

<!-- Available Deliveries Section -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Livraisons disponibles (8)</h2>
    </div>
    <div class="p-4">
        <div class="space-y-4">
            <!-- Delivery Item 1 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                    <div>
                        <h3 class="font-medium text-gray-900">Commande #2593</h3>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Almadies, Dakar
                            </span>
                            <span class="mx-2">•</span>
                            <span>3.8 km</span>
                            <span class="mx-2">•</span>
                            <span>20 min estimés</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Restaurant
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                1,200 FCFA
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Taille: Petit
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg shadow-sm">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                            Accepter
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Item 2 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                    <div>
                        <h3 class="font-medium text-gray-900">Commande #2598</h3>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Mermoz, Dakar
                            </span>
                            <span class="mx-2">•</span>
                            <span>5.2 km</span>
                            <span class="mx-2">•</span>
                            <span>25 min estimés</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Supermarché
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                1,500 FCFA
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Taille: Moyen
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg shadow-sm">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                            Accepter
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Item 3 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                    <div>
                        <h3 class="font-medium text-gray-900">Commande #2601</h3>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Yoff, Dakar
                            </span>
                            <span class="mx-2">•</span>
                            <span>4.1 km</span>
                            <span class="mx-2">•</span>
                            <span>18 min estimés</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                Pharmacie
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                1,800 FCFA
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Urgent
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg shadow-sm">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                            Accepter
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- More delivery items would go here -->
            <!-- Delivery Item 4 -->
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                    <div>
                        <h3 class="font-medium text-gray-900">Commande #2605</h3>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> Médina, Dakar
                            </span>
                            <span class="mx-2">•</span>
                            <span>6.5 km</span>
                            <span class="mx-2">•</span>
                            <span>30 min estimés</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Vêtements
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                2,000 FCFA
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Taille: Grand
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg shadow-sm">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                            Accepter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center mt-6">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    1
                </a>
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-green-50 text-sm font-medium text-green-600 hover:bg-green-100">
                    2
                </a>
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    3
                </a>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    ...
                </span>
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    8
                </a>
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </nav>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Placeholder for future map implementation
    document.addEventListener('DOMContentLoaded', function() {
        // Ici nous pourrions initialiser une carte avec Leaflet ou Google Maps API
        console.log("Map initialization placeholder");
    });
</script>
@endsection
@extends('layouts.template')

@section('title', 'Tableau de bord')

@section('content')
<div class="bg-gray-50 min-h-screen pb-12">
        <!-- En-tête -->
        <div class="bg-indigo-600 py-6">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-white">Historique des livraisons</h1>
                    <a href="{{ route('delivery.new') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Nouvelle livraison
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="px-6 py-4">
                    <form method="GET" action="{{ route('delivery.history') }}" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4">
                        <div class="flex-1">
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select id="status" name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="in_progress">En cours</option>
                                <option value="delivered">Livré</option>
                                <option value="cancelled">Annulé</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" id="date_from" name="date_from" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="flex-1">
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Date de fin</label>
                            <input type="date" id="date_to" name="date_to" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="md:self-end">
                            <button type="submit" class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des livraisons -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Vos livraisons récentes</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    <!-- Exemple de livraison 1 (récente) -->
                    <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="flex items-start">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Colis #DEL-12345</div>
                                    <div class="text-sm text-gray-500">15 Avril 2025</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0">
                            <div class="text-sm text-gray-900">De: 123 Rue du Commerce, Paris</div>
                            <div class="text-sm text-gray-900">À: 456 Avenue des Clients, Paris</div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0 md:text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                En cours
                            </span>
                            <div class="text-sm text-gray-500 mt-1">Livraison estimée: 16:30</div>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <a href="{{ route('delivery.show', 12345) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                        </div>
                    </div>
                    
                    <!-- Exemple de livraison 2 (terminée) -->
                    <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="flex items-start">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Colis #DEL-12344</div>
                                    <div class="text-sm text-gray-500">12 Avril 2025</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0">
                            <div class="text-sm text-gray-900">De: 20 Rue du Marché, Paris</div>
                            <div class="text-sm text-gray-900">À: 30 Boulevard Haussman, Paris</div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0 md:text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Livré
                            </span>
                            <div class="text-sm text-gray-500 mt-1">Livré le: 12 Avril, 14:25</div>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <a href="{{ route('delivery.show', 12344) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                        </div>
                    </div>
                    
                    <!-- Exemple de livraison 3 (annulée) -->
                    <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="flex items-start">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Colis #DEL-12343</div>
                                    <div class="text-sm text-gray-500">10 Avril 2025</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0">
                            <div class="text-sm text-gray-900">De: 5 Avenue Montaigne, Paris</div>
                            <div class="text-sm text-gray-900">À: 100 Rue de Rivoli, Paris</div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0 md:text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Annulé
                            </span>
                            <div class="text-sm text-gray-500 mt-1">Annulé le: 10 Avril, 09:15</div>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <a href="{{ route('delivery.show', 12343) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                        </div>
                    </div>
                    
                    <!-- Exemple de livraison 4 (en attente) -->
                    <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="flex items-start">
                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Colis #DEL-12342</div>
                                    <div class="text-sm text-gray-500">8 Avril 2025</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0">
                            <div class="text-sm text-gray-900">De: 78 Rue de la Tour, Paris</div>
                            <div class="text-sm text-gray-900">À: 42 Avenue des Champs-Élysées, Paris</div>
                        </div>
                        <div class="flex-1 mt-2 md:mt-0 md:text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                En attente
                            </span>
                            <div class="text-sm text-gray-500 mt-1">Programmé: 17 Avril, 13:00-15:00</div>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <a href="{{ route('delivery.show', 12342) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Précédent
                        </a>
                        <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Suivant
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                        Affichage de
                                <span class="font-medium">1</span>
                                à
                                <span class="font-medium">4</span>
                                sur
                                <span class="font-medium">12</span>
                                livraisons
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" aria-label="Précédent">
                                    <span>&laquo;</span>
                                </a>
                                <a href="#" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">1</a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">2</a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 hidden md:inline-flex relative items-center px-4 py-2 border text-sm font-medium">3</a>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">6</a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" aria-label="Suivant">
                                    <span>&raquo;</span>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
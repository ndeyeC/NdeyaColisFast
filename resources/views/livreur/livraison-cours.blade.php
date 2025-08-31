@extends('layouts.master')

@section('title', 'Livraisons en cours')

@section('page-title', 'Livraisons en cours')


@section('styles')
    <style>
        
        #leafletMap {
            height: 300px; 
            width: 100%;
            min-height: 200px; /* Hauteur minimale pour petits écrans */
        }
        .start-marker, .end-marker, .current-position {
            text-align: center;
        }
        .leaflet-popup-content-wrapper {
            max-width: 200px;
        }
        #routeInstructions {
            max-height: 120px; /* Réduire légèrement pour plus de place */
            overflow-y: auto;
        }
        /* Ajouter un défilement au contenu du modal si nécessaire */
        .modal-content-container {
            max-height: 80vh; /* Limiter la hauteur à 80% de l'écran */
            overflow-y: auto; /* Activer le défilement vertical */
            padding: 1rem;
        }
        .modal-footer {
            position: sticky;
            bottom: 0;
            background: white; /* Fond blanc pour éviter la transparence */
            padding: 1rem;
            border-top: 1px solid #e5e7eb; /* Séparation visuelle */
        }
    </style>
@endsection

@section('content')
<!-- Current Delivery Section -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Livraison actuelle</h2>
    </div>
    
    <div id="currentDeliveryContainer" class="p-4">
        @if($livraisonActuelle)
            <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0">
                <!-- Delivery Info Column -->
                <div class="w-full md:w-2/3 pr-0 md:pr-4">
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-box text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Commande #{{ $livraisonActuelle->reference }}</h3>
                                <p class="text-sm text-gray-600">{{ $livraisonActuelle->adresse_depart }}</p>
                            </div>
                            <div class="ml-auto">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    En cours
                                </span>
                            </div>
                        </div>
                        
                        <!-- Delivery Steps -->
                        <div class="flex flex-col space-y-3">
                            <!-- Pickup Location -->
                            <div class="flex items-start">
                                <div class="flex items-center justify-center bg-green-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-store text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700">Point de ramassage</h4>
                                    <p class="text-sm text-gray-600">{{ $livraisonActuelle->adresse_depart }}</p>
                                    @if($livraisonActuelle->details_adresse_depart)
                                        <p class="text-xs text-gray-500">{{ $livraisonActuelle->details_adresse_depart }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Delivery Location -->
                            <div class="flex items-start">
                                <div class="flex items-center justify-center bg-red-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-map-marker-alt text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700">Point de livraison</h4>
                                    <p class="text-sm text-gray-600">{{ $livraisonActuelle->adresse_arrivee }}</p>
                                    @if($livraisonActuelle->details_adresse_arrivee)
                                        <p class="text-xs text-gray-500">{{ $livraisonActuelle->details_adresse_arrivee }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="flex justify-between mb-1 text-xs text-gray-600">
                                <span>Progression</span>
                                <span id="deliveryProgressPercentage">{{ $progressPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="deliveryProgressBar" class="bg-red-500 h-2 rounded-full" 
                                     style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Delivery Stats -->
                        <div class="flex flex-col space-y-2 mt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Distance restante:</span>
                                <span id="remainingDistance" class="font-medium">
                                    {{ $livraisonActuelle->deliveryRoute ? $livraisonActuelle->deliveryRoute->distance_km : 'N/A' }} km
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Temps estimé:</span>
                                <span id="estimatedTime" class="font-medium">
                                    {{ $livraisonActuelle->deliveryRoute ? $livraisonActuelle->deliveryRoute->duration_minutes : 'N/A' }} min
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Client:</span>
                                <span class="font-medium">{{ $livraisonActuelle->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Prix:</span>
                                 <span class="font-medium">{{ number_format($livraisonActuelle->prix_final, 0, '.', '') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <button onclick="openNavigation({{ $livraisonActuelle->id }})" 
                                class="flex-1 bg-red-500 hover:bg-red-500 text-white px-4 py-3 rounded-lg shadow-sm flex items-center justify-center">
                               <i class="fas fa-route mr-2"></i>
                            Ouvrir la navigation
                        </button>
                        <a href="tel:{{ $livraisonActuelle->user->numero_telephone ?? '' }}" 
                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg shadow-sm flex items-center justify-center">
                            <i class="fas fa-phone"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Map Column -->
                <div class="w-full md:w-1/3">
                    
                    <!-- Delivery Actions -->
                    <div class="bg-white rounded-lg shadow-sm mt-4 p-4 border border-gray-200">
                        <h3 class="font-medium text-gray-900 mb-2">Actions</h3>
                        <div class="space-y-2">
                            <button onclick="markAsDelivered({{ $livraisonActuelle->id }})" 
                                    class="w-full text-left bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded flex items-center">
                                <i class="fas fa-clipboard-check text-green-600 mr-2"></i>
                                Marquer comme livré
                            </button>
                            <button onclick="showProblemModal({{ $livraisonActuelle->id }})" 
                                    class="w-full text-left bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                Signaler un problème
                            </button>
                            <button onclick="cancelDelivery({{ $livraisonActuelle->id }})" 
                                    class="w-full text-left bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded flex items-center">
                                <i class="fas fa-ban text-red-600 mr-2"></i>
                                Annuler la livraison
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Aucune livraison en cours actuellement</p>
                <a href="{{ route('livreur.livraisons-disponible') }}" 
                   class="mt-4 inline-block bg-red-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search mr-2"></i> Voir les livraisons disponibles
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Pending Deliveries Section -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Livraisons en attente</h2>
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ count($livraisonsEnAttente) }}
            </span>
        </div>
    </div>
    
    <div class="p-4">
        @if(count($livraisonsEnAttente) > 0)
            <div class="space-y-4">
                @foreach($livraisonsEnAttente as $livraison)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                        <div class="flex items-start">
                            <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-shopping-bag text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Commande #{{ $livraison->reference }}</h3>
                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                    <span class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> 
                                        {{ Str::limit($livraison->adresse_depart, 30) }}
                                    </span>
                                    <span class="mx-2">→</span>
                                    <span>{{ Str::limit($livraison->adresse_arrivee, 30) }}</span>
                                </div>
                                <div class="mt-2 flex space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        En attente
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                      {{ number_format($livraison->prix_final, 0, '.', '') }} FCFA
                                    </span>
                                    @if($livraison->type_livraison === 'express')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Express
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button onclick="startDelivery({{ $livraison->id }})" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                                <i class="fas fa-play mr-2"></i>
                                Démarrer
                            </button>
                            <button onclick="cancelDelivery({{ $livraison->id }})" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Aucune livraison en attente</p>
            </div>
        @endif
    </div>
</div>

<!-- Statistics Section -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                <i class="fas fa-box-open"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Livraisons aujourd'hui</p>
                <p class="text-xl font-semibold">{{ $statistiques['livraisons_jour'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Revenus aujourd'hui</p>
                <p class="text-xl font-semibold">{{ number_format($statistiques['revenus_jour']) }} FCFA</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-3">
                <i class="fas fa-truck-moving"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">En cours</p>
                <p class="text-xl font-semibold">{{ $statistiques['en_cours'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">En attente</p>
                <p class="text-xl font-semibold">{{ $statistiques['en_attente'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Mark as Delivered Modal -->
<div id="deliveredModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la livraison</h3>
                <form id="deliveredForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                            <textarea name="commentaire_livraison" rows="3" class="w-full rounded-lg border-gray-300"></textarea>
                        </div>
                       
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('deliveredModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                            Annuler
                        </button>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            Confirmer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Problem Modal -->
<div id="problemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Signaler un problème</h3>
                <div id="problemFormErrors" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
                
                <form id="problemForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de problème *</label>
                            <select name="type_probleme" required class="w-full rounded-lg border-gray-300">
                                <option value="">Sélectionnez un type</option>
                                <option value="client_absent">Client absent</option>
                                <option value="adresse_incorrecte">Adresse incorrecte</option>
                                <option value="colis_endommage">Colis endommagé</option>
                                <option value="autre">Autre problème</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                            <textarea name="description" rows="3" required class="w-full rounded-lg border-gray-300" placeholder="Décrivez le problème rencontré..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Photo (optionnelle)</label>
                            <input type="file" name="photo" accept="image/*" class="w-full rounded-lg border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">Taille max: 2MB (JPEG, PNG)</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('problemModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                            Annuler
                        </button>
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <span id="submitBtnText">Signaler</span>
                            <i id="submitBtnSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Delivery Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Annuler la livraison</h3>
                
                <!-- Conteneur pour les erreurs -->
                <div id="cancelFormErrors" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
                
                <form id="cancelForm" method="POST" action="">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Raison de l'annulation *
                        </label>
                        <textarea 
                            name="raison" 
                            rows="3" 
                            required 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Expliquez pourquoi vous annulez cette livraison..."
                        ></textarea>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('cancelModal')" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            <span class="submit-text">Confirmer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Options Modal -->
<!-- Navigation Options Modal -->
<div id="navigationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="modal-content-container">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Navigation</h3>
                    <div id="leafletMap" style="height: 300px; width: 100%;"></div>
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700">Instructions de l'itinéraire</h4>
                        <div id="routeInstructions" class="text-sm text-gray-600 max-h-120 overflow-y-auto"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="closeModal('navigationModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

@if($livraisonActuelle)
<meta name="current-delivery-id" content="{{ $livraisonActuelle->id }}">
@endif

<script src="{{ asset('js/livraisoncours.js') }}"></script>


@endsection
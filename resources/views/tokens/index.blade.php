@extends('layouts.template')

@section('content')
<div class="p-4 max-w-5xl mx-auto">
    <!-- En-tête avec bouton de retour -->
    <div class="flex items-center mb-6">
        <a href="{{ url()->previous() }}" class="mr-4 text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        
        <h2 class="font-bold text-2xl flex items-center">
            <i class="fas fa-coins text-yellow-500 mr-3"></i> Mes Jetons de Livraison
        </h2>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Colonne gauche - Liste des villes -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-700">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i> Mes villes
                    </h3>
                    
                    <div class="space-y-3">
                        @forelse($zones as $zone)
                            @php
                                $zoneTokens = $validTokens[$zone->id]->total ?? 0;
                            @endphp
                            <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg transition cursor-pointer"
                                 onclick="selectZone({{ $zone->id }}, '{{ $zone->name }}', {{ $zone->base_token_price }})">
                                <span class="font-medium">{{ $zone->name }}</span>
                                <span class="font-bold {{ $zoneTokens > 0 ? 'text-green-500' : 'text-gray-400' }}">
                                    {{ $zoneTokens }} jeton{{ $zoneTokens > 1 ? 's' : '' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 py-2 text-center">
                                <i class="fas fa-info-circle mr-2"></i> Aucune zone disponible
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite - Formulaire d'achat -->
        <div class="w-full md:w-2/3">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-700">
                        <i class="fas fa-plus-circle mr-2 text-green-500"></i> Acheter des jetons
                    </h3>

                    <form method="POST" action="{{ route('tokens.purchase') }}" id="tokenForm">
                        @csrf
                        <input type="hidden" name="zone_id" id="zoneIdInput" value="">
                        
                        <!-- Zone sélectionnée -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg" id="selectedZoneContainer" style="display: none;">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-blue-800">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        <span id="selectedZoneName">-</span>
                                    </p>
                                    <p class="text-sm text-blue-600 mt-1">
                                        Prix: <span id="selectedZonePrice">0</span> FCFA/jeton
                                    </p>
                                </div>
                                <button type="button" onclick="clearZoneSelection()" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-times mr-1"></i> Changer
                                </button>
                            </div>
                        </div>

                        <!-- Sélection du nombre de jetons -->
                        <div class="mb-6">
                            <label class="block mb-3 font-medium text-gray-700">Quantité de jetons</label>
                            
                            <div class="grid grid-cols-4 gap-3 mb-4">
                                @foreach([1, 2, 5, 10] as $amount)
                                    <button type="button" onclick="selectTokenAmount({{ $amount }}, this)"
                                        class="border border-gray-200 rounded-lg p-3 text-center hover:border-blue-300 transition token-option">
                                        <p class="font-bold text-gray-800">{{ $amount }}</p>
                                        <p class="text-xs text-gray-500">{{ $amount > 1 ? 'jetons' : 'jeton' }}</p>
                                    </button>
                                @endforeach
                            </div>
                            
                            <!-- Option personnalisée -->
                            <div class="flex items-center gap-3">
                                <input type="number" id="customAmount" min="1" max="50" placeholder="Autre quantité" 
                                       class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" 
                                       oninput="selectCustomAmount(this.value)">
                                <button type="button" onclick="applyCustomAmount()" 
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition">
                                    OK
                                </button>
                            </div>
                        </div>
                        
                        <!-- Résumé -->
                        <div id="selectionContainer" class="hidden mb-6 p-4 bg-blue-50 rounded-lg transition-all">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-blue-800">
                                        <span id="selectedQuantityDisplay">0</span> jeton(s) sélectionné(s)
                                    </p>
                                    <p class="text-sm text-blue-600 mt-1">
                                        <i class="fas fa-coins mr-1"></i> Total : <span id="selectedAmountFcfa" class="font-bold">0</span> FCFA
                                    </p>
                                </div>
                                <button type="button" onclick="clearSelection()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-times mr-1"></i> Annuler
                                </button>
                            </div>
                        </div>
                        
                        <input type="hidden" name="amount" id="tokenAmountInput" value="">
                        
                        <!-- Méthode de paiement -->
                        <div class="mb-6">
                            <label class="block mb-2 font-medium text-gray-700">Méthode de paiement</label>
                            <select name="payment_method" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" required>
                                <option value="">-- Sélectionnez --</option>
                                <option value="wave">Wave</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="credit_card">Carte de crédit</option>
                            </select>
                        </div>
                        
                        <!-- Bouton de soumission -->
                        <button type="submit" id="submitButton" disabled
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-lg hover:from-blue-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-md">
                            <i class="fas fa-shopping-cart mr-2"></i> Payer maintenant
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Historique (optionnel, peut être masqué) -->
        <!-- Historique des transactions -->
<div class="mt-6 bg-white rounded-xl shadow-md overflow-hidden">
    <div class="p-6">
        <h3 class="font-bold text-lg mb-4 text-gray-700 flex items-center">
            <i class="fas fa-history mr-2 text-purple-500"></i> Dernières transactions
        </h3>
        
        <div class="space-y-3 max-h-60 overflow-y-auto">
            @forelse($transactions as $transaction)
                <div class="border-b border-gray-100 pb-3 last:border-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="{{ $transaction->amount > 0 ? 'text-green-500' : 'text-red-500' }} font-medium">
                                {{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->amount }} jeton{{ $transaction->amount != 1 ? 's' : '' }}
                            </p>
                            @if($transaction->zone)
                                <p class="text-xs text-gray-500">{{ $transaction->zone->name }}</p>
                            @endif
                            
                            <!-- Affichage de la validité -->
                            @if($transaction->amount > 0 && $transaction->expiry_date)
                                <p class="text-xs mt-1 {{ $transaction->isExpired() ? 'text-red-500' : 'text-blue-500' }}">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    Valide jusqu'au {{ $transaction->expiry_date->format('d/m/Y') }}
                                    @if(!$transaction->isExpired())
                                        ({{ $transaction->daysUntilExpiry() }} jour{{ $transaction->daysUntilExpiry() > 1 ? 's' : '' }})
                                    @else
                                        (Expiré)
                                    @endif
                                </p>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 whitespace-nowrap">
                            {{ $transaction->created_at->format('d/m H:i') }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">
                    <i class="fas fa-box-open mr-2"></i> Aucune transaction
                </p>
            @endforelse
         </div>
        </div>
       </div>
        </div>
       </div>
     </div>

<script>
    let selectedZoneId = '';
    let selectedTokens = 0;
    let tokenPrice = 0;
    
    // Sélectionner une zone depuis la liste
    function selectZone(zoneId, zoneName, price) {
        selectedZoneId = zoneId;
        tokenPrice = price;
        
        // Mettre à jour l'affichage
        document.getElementById('zoneIdInput').value = zoneId;
        document.getElementById('selectedZoneName').textContent = zoneName;
        document.getElementById('selectedZonePrice').textContent = price.toLocaleString();
        document.getElementById('selectedZoneContainer').style.display = 'block';
        
        // Réinitialiser la sélection de jetons
        clearSelection();
    }
    
    // Effacer la sélection de zone
    function clearZoneSelection() {
        selectedZoneId = '';
        document.getElementById('zoneIdInput').value = '';
        document.getElementById('selectedZoneContainer').style.display = 'none';
        clearSelection();
    }
    
    // Sélectionner un nombre de jetons
    function selectTokenAmount(amount, element) {
        if (!selectedZoneId) {
            alert('Veuillez d\'abord sélectionner une ville');
            return;
        }
        
        document.getElementById('customAmount').value = '';
        selectedTokens = amount;
        updateSelectionDisplay(amount, amount * tokenPrice);
        
        // Mise en évidence
        document.querySelectorAll('.token-option').forEach(btn => {
            btn.classList.remove('bg-blue-100', 'border-blue-400');
        });
        element.classList.add('bg-blue-100', 'border-blue-400');
    }
    
    // Appliquer un montant personnalisé
    function applyCustomAmount() {
        if (!selectedZoneId) {
            alert('Veuillez d\'abord sélectionner une ville');
            return;
        }
        
        const amount = parseInt(document.getElementById('customAmount').value);
        if (isNaN(amount) || amount < 1) {
            alert('Veuillez entrer un nombre valide');
            return;
        }
        
        selectedTokens = amount;
        updateSelectionDisplay(amount, amount * tokenPrice);
        
        // Désélectionner les boutons
        document.querySelectorAll('.token-option').forEach(btn => {
            btn.classList.remove('bg-blue-100', 'border-blue-400');
        });
    }
    
    // Mettre à jour l'affichage
    function updateSelectionDisplay(quantity, total) {
        document.getElementById('tokenAmountInput').value = quantity;
        document.getElementById('selectedQuantityDisplay').textContent = quantity;
        document.getElementById('selectedAmountFcfa').textContent = total.toLocaleString();
        document.getElementById('selectionContainer').style.display = 'block';
        document.getElementById('submitButton').disabled = false;
    }
    
    // Effacer la sélection
    function clearSelection() {
        selectedTokens = 0;
        document.getElementById('tokenAmountInput').value = '';
        document.getElementById('customAmount').value = '';
        document.getElementById('selectionContainer').style.display = 'none';
        document.getElementById('submitButton').disabled = true;
        
        document.querySelectorAll('.token-option').forEach(btn => {
            btn.classList.remove('bg-blue-100', 'border-blue-400');
        });
    }
    
    // Validation du formulaire
    document.getElementById('tokenForm').addEventListener('submit', function(e) {
        if (!selectedZoneId || !selectedTokens) {
            e.preventDefault();
            alert('Veuillez sélectionner une ville et une quantité de jetons');
        }
    });
</script>
@endsection
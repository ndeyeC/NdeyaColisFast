@extends('layouts.template')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-gray-50">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ url()->previous() }}" class="mr-4 text-red-600 hover:text-red-800 transition" aria-label="Retour à la page précédente">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-coins text-red-500 mr-2"></i> Mes Jetons de Livraison - Dakar
        </h2>
    </div>

    <!-- Information Banner -->
    <div class="mb-8 p-5 bg-red-100 border border-red-200 rounded-lg shadow-sm">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-red-600 text-lg mr-3 mt-1"></i>
            <div>
                <h3 class="text-lg font-semibold text-red-800 mb-2">Jetons pour Dakar</h3>
                <p class="text-sm text-red-700">
                    Ces jetons sont utilisables uniquement pour les livraisons dans la région de Dakar.
                </p>
                <ul class="text-sm text-red-700 mt-2 space-y-1 list-disc list-inside">
                    <li><strong>Zone couverte</strong> : Toute la région de Dakar</li>
                    <li><strong>Autres régions</strong> : Paiement en ligne requis.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Zones Section -->
        <div class="md:col-span-1 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-map-marker-alt text-red-600 mr-2"></i> Zone Disponible
            </h3>
            <div class="space-y-3">
                @forelse($zones->where('name', 'Dakar') as $zone)
                    @php
                        $zoneTokens = $validTokens[$zone->id]->total ?? 0;
                    @endphp
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-red-50 transition border border-gray-200"
                         onclick="selectZone({{ $zone->id }}, '{{ $zone->name }}', {{ $zone->base_token_price }})"
                         role="button" tabindex="0" aria-label="Sélectionner la zone {{ $zone->name }}">
                        <div>
                            <span class="font-medium text-gray-800">{{ $zone->name }}</span>
                            <p class="text-xs text-gray-500 mt-1">{{ $zone->description }}</p>
                        </div>
                        <div class="text-center">
                            <span class="font-semibold {{ $zoneTokens > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                {{ $zoneTokens }}
                            </span>
                            <p class="text-xs text-gray-500">jeton{{ $zoneTokens > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-exclamation-circle text-yellow-500 text-xl mb-2"></i>
                        <p class="text-sm text-gray-600">Aucune zone configurée. Contactez le support.</p>
                    </div>
                @endforelse
            </div>
            <!-- Token Benefits -->
           <div class="mt-6 p-4 bg-red-50 rounded-lg">
                <h4 class="font-semibold text-gray-800 mb-2">Règles d'Utilisation des Jetons</h4>
                <p class="text-sm text-gray-700">
                    Après l'achat de jetons, pour créer une commande, veuillez noter :
                </p>
                <ul class="text-sm text-gray-700 mt-2 space-y-1 list-disc list-inside">
                    <li>Les champs <strong>adresse de départ</strong> et <strong>destination</strong> doivent être dans la région de <strong>Dakar</strong>.</li>
                    <li>Les jetons sont valides uniquement pour les livraisons dans la zone de Dakar.</li>
                </ul>
            </div>
        </div>

        <!-- Purchase Form -->
        <div class="md:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-plus-circle text-red-600 mr-2"></i> Acheter des Jetons
            </h3>
            <form method="POST" action="{{ route('tokens.purchase') }}" id="tokenForm">
                @csrf
                <input type="hidden" name="zone_id" id="zoneIdInput" value="">
                <div class="mb-6 p-4 bg-red-50 rounded-lg hidden" id="selectedZoneContainer">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-red-800">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span id="selectedZoneName">-</span>
                            </p>
                            <p class="text-sm text-red-600 mt-1">
                                Prix : <span id="selectedZonePrice">0</span> FCFA/jeton
                            </p>
                        </div>
                        <button type="button" onclick="clearZoneSelection()" class="text-red-600 hover:text-red-800 text-sm" aria-label="Changer de zone">
                            <i class="fas fa-times mr-1"></i> Changer
                        </button>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block mb-2 font-medium text-gray-700" for="customAmount">Quantité de Jetons</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                        @foreach([1, 2, 5, 10] as $amount)
                            <button type="button" onclick="selectTokenAmount({{ $amount }}, this)"
                                    class="border border-gray-200 rounded-lg p-3 text-center hover:bg-red-50 hover:border-red-300 transition token-option"
                                    aria-label="Sélectionner {{ $amount }} jeton(s)">
                                <p class="font-semibold text-gray-800">{{ $amount }}</p>
                                <p class="text-xs text-gray-500">{{ $amount > 1 ? 'jetons' : 'jeton' }}</p>
                            </button>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="number" id="customAmount" min="1" max="50" placeholder="Quantité personnalisée"
                               class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200"
                               aria-label="Saisir une quantité personnalisée de jetons">
                        <button type="button" onclick="applyCustomAmount()"
                                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                            OK
                        </button>
                    </div>
                </div>
                <div id="selectionContainer" class="hidden mb-6 p-4 bg-red-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-red-800">
                                <span id="selectedQuantityDisplay">0</span> jeton(s) sélectionné(s)
                            </p>
                            <p class="text-sm text-red-600 mt-1">
                                Total : <span id="selectedAmountFcfa" class="font-semibold">0</span> FCFA
                            </p>
                        </div>
                        <button type="button" onclick="clearSelection()" class="text-red-600 hover:text-red-800" aria-label="Annuler la sélection">
                            <i class="fas fa-times mr-1"></i> Annuler
                        </button>
                    </div>
                </div>
                <input type="hidden" name="amount" id="tokenAmountInput" value="">
                <button type="submit" id="submitButton" disabled
                        class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-md">
                    <i class="fas fa-credit-card mr-2"></i> Payer avec PayDunya
                </button>
                <p class="mt-4 text-center text-xs text-gray-500">
                   Les jetons ont une durée de validité limitée d'une semaine.
               </p>
            </form>
        </div>
    </div>

    <!-- Transaction History -->
   <div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-history text-red-600 mr-2"></i> Historique des Jetons
    </h3>
    <div class="space-y-4 max-h-80 overflow-y-auto">
        @forelse($transactions as $transaction)
            <div class="border-b border-gray-200 pb-4 last:border-0">
                <div class="flex justify-between items-start">
                    <div>
                        @if($transaction->amount > 0)
                            <p class="text-gray-800 font-semibold">
                                +{{ $transaction->amount }} jeton{{ $transaction->amount != 1 ? 's' : '' }}
                            </p>
                        @elseif($transaction->amount < 0)
                            <p class="text-red-600 font-semibold">
                                {{ $transaction->amount }} jeton{{ abs($transaction->amount) != 1 ? 's' : '' }} utilisé
                            </p>
                        @endif

                        @if($transaction->zone)
                            <p class="text-xs text-gray-500 flex items-center mt-1">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i> {{ $transaction->zone->name }}
                            </p>
                        @endif

                        @if($transaction->amount > 0 && $transaction->expiry_date)
                            <p class="text-xs mt-1 {{ $transaction->isExpired() ? 'text-yellow-600' : 'text-gray-500' }}">
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
                    <div class="text-right">
                        <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m H:i') }}</p>
                        <p class="text-xs font-medium {{ $transaction->amount < 0 ? 'text-red-600' : 'text-gray-600' }}">
                            {{ $transaction->amount > 0 ? 'Achat' : 'Livraison' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-6">
                <i class="fas fa-box-open text-gray-400 text-2xl mb-2"></i>
                <p class="text-sm text-gray-600">Aucune transaction enregistrée.</p>
                <p class="text-xs text-gray-500 mt-1">Achetez vos premiers jetons pour commencer !</p>
            </div>
        @endforelse
    </div>
</div>

    <!-- Usage Guide -->
    <div class="mt-8 bg-red-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-question-circle text-red-600 mr-2"></i> Comment Utiliser Vos Jetons
        </h3>
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4 shadow-sm text-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shopping-cart text-red-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">1. Achetez</h4>
                <p class="text-sm text-gray-600">Choisissez une quantité de jetons pour Dakar.</p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm text-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-plus-circle text-red-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">2. Commandez</h4>
                <p class="text-sm text-gray-600">Créez une commande à Dakar avec vos jetons.</p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm text-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-truck text-red-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">3. Livraison</h4>
                <p class="text-sm text-gray-600">Profitez d'une livraison rapide sans paiement.</p>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/token.js') }}"></script>
@endsection
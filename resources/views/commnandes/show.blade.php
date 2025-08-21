
@extends('layouts.template')

@section('title', 'Détails de la commande')

@section('content')
<div class="max-w-4xl mx-auto mt-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('commnandes.create') }}" class="flex items-center text-blue-600 hover:text-blue-800 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour 
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
        <!-- Message de confirmation -->
        @if(session('success') && $commnande->status == \App\Models\Commnande::STATUT_CONFIRMEE)
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p>Votre commande a été créée avec succès. Vous recevrez un SMS lorsque votre commande sera acceptée et livrée.</p>
            </div>
        @endif
        
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Détails de la commande #{{ $commnande->id }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Adresse de départ</h3>
                    <p class="text-gray-600">{{ $commnande->adresse_depart }}</p>
                    @if($commnande->details_adresse_depart)
                        <p class="text-gray-500 text-sm">{{ $commnande->details_adresse_depart }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Adresse d'arrivée</h3>
                    <p class="text-gray-600">{{ $commnande->adresse_arrivee }}</p>
                    @if($commnande->details_adresse_arrivee)
                        <p class="text-gray-500 text-sm">{{ $commnande->details_adresse_arrivee }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Date</h3>
                    <p class="text-gray-600">{{ $commnande->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Statut</h3>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full 
                        @if($commnande->status == \App\Models\Commnande::STATUT_LIVREE) bg-green-100 text-green-800
                        @elseif($commnande->status == \App\Models\Commnande::STATUT_ANNULEE) bg-red-100 text-red-800
                        @elseif($commnande->status == \App\Models\Commnande::STATUT_EN_ATTENTE) bg-yellow-100 text-yellow-800
                        @elseif($commnande->status == \App\Models\Commnande::STATUT_PAYEE) bg-green-100 text-green-800
                        @elseif($commnande->status == \App\Models\Commnande::STATUT_CONFIRMEE) bg-blue-100 text-blue-800
                        @elseif($commnande->status == \App\Models\Commnande::STATUT_ACCEPTEE) bg-blue-100 text-blue-800
                        @elseif($commnande->status == \App\Models\Commnande::STATUT_EN_COURS) bg-orange-100 text-orange-800
                        @else bg-gray-100 text-gray-800 @endif">
                        @switch($commnande->status)
                            @case(\App\Models\Commnande::STATUT_LIVREE) Livrée @break
                            @case(\App\Models\Commnande::STATUT_ANNULEE) Annulée @break
                            @case(\App\Models\Commnande::STATUT_EN_ATTENTE) En attente de paiement @break
                            @case(\App\Models\Commnande::STATUT_PAYEE) Payée @break
                            @case(\App\Models\Commnande::STATUT_CONFIRMEE) Confirmée @break
                            @case(\App\Models\Commnande::STATUT_ACCEPTEE) Acceptée @break
                            @case(\App\Models\Commnande::STATUT_EN_COURS) En cours @break
                            @default Inconnu
                        @endswitch
                    </span>
                </div>
            </div>
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Montant</h3>
                    <p class="text-gray-600">{{ number_format($commnande->prix_final, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>

        <div class="mb-6 flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Livreur</h3>
                @if($commnande->driver)
                    <p class="text-gray-600">{{ $commnande->driver->name }}</p>
                    <!-- Rating à implémenter ultérieurement si nécessaire -->
                    {{-- @if($commnande->driver->rating)
                        <div class="flex items-center mt-1">
                            <span class="text-yellow-500">★</span>
                            <span class="ml-1 text-gray-600">{{ $commnande->driver->rating }}/5</span>
                        </div>
                    @endif --}}
                @else
                    <p class="text-gray-600 italic">Aucun livreur assigné</p>
                @endif
            </div>
        </div>

        <!-- Bouton de confirmation si statut est payee ou en_attente_paiement -->
        <!-- @if(in_array($commnande->status, [\App\Models\Commnande::STATUT_PAYEE, \App\Models\Commnande::STATUT_EN_ATTENTE]))
            <div class="mt-6">
                <form action="{{ route('commnandes.confirm', $commnande->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Confirmer la commande
                    </button>
                </form>
            </div>
        @endif -->

        <!-- Bouton de confirmation si statut est payee ou en_attente_paiement -->
@if(in_array($commnande->status, [\App\Models\Commnande::STATUT_PAYEE, \App\Models\Commnande::STATUT_EN_ATTENTE]))
    <div class="mt-6">
        <button id="confirm-btn"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Confirmer la commande
        </button>
    </div>
@endif


    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const confirmBtn = document.getElementById("confirm-btn");

    if (confirmBtn) {
        confirmBtn.addEventListener("click", function() {
            confirmBtn.disabled = true;
            confirmBtn.innerText = "Confirmation en cours...";

            fetch("{{ route('commnandes.confirm', $commnande->id) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Met à jour le statut affiché
                    document.querySelector("p span").innerText = "Confirmée";
                    document.querySelector("p span").className =
                        "px-2 py-1 rounded text-white bg-green-600";

                    // Supprime le bouton
                    confirmBtn.remove();

                    // Petite notif
                    alert("✅ " + data.message);
                } else {
                    alert("❌ " + data.message);
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = "Confirmer la commande";
                }
            })
            .catch(err => {
                console.error(err);
                alert("⚠️ Erreur serveur");
                confirmBtn.disabled = false;
                confirmBtn.innerText = "Confirmer la commande";
            });
        });
    }
});
</script>

@endsection

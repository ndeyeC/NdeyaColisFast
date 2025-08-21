@extends('layouts.template')

@section('title', 'ColisFast - Client')

@section('content')
<div class="bg-gray-50 min-h-screen pb-16">
    <div class="max-w-md mx-auto bg-white shadow-sm relative min-h-screen">


        <!-- === ONGLET ACCUEIL === -->
        <div id="homeTab" class="tab-content">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-5 rounded-b-3xl shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-bold">👋 Bonjour, <span>{{ Auth::user()->name ?? Auth::user()->email }}</span></h1>
                        <p class="text-sm opacity-90 mt-1">Solde actuel</p>
                        <div class="text-xl font-extrabold text-yellow-300">{{ Auth::user()->token_balance ?? 0 }} jetons</div>
                    </div>
                    <div class="bg-white/20 rounded-full px-3 py-1 flex items-center shadow-inner">
                        <i class="fas fa-coins text-yellow-300 mr-2"></i>
                    </div>
                </div>
            </div>

            <!-- CTA Nouvelle Livraison -->
            <div class="p-4">
                <button onclick="window.location.href='{{ url('commnandes/create') }}'" 
                    class="w-full bg-red-600 shadow hover:shadow-lg text-white py-4 px-4 rounded-xl font-bold flex items-center justify-center transition transform hover:scale-[1.02]">
                    <i class="fas fa-plus-circle mr-2"></i> Nouvelle Livraison
                </button>
            </div>

            <!-- Livraison en cours -->
            <div class="px-4">
                <h2 class="font-bold text-lg mb-3 flex items-center">
                    <i class="fas fa-truck-moving text-red-600 mr-2"></i> Livraison en cours
                </h2>
                
                <div id="commandeEnCours">
                    @if(isset($commandeEnCours) && $commandeEnCours)
                        @include('client.partials.commande-en-cours', ['commande' => $commandeEnCours])
                    @else
                        <div class="bg-gray-50 border border-gray-200 p-5 rounded-xl text-center shadow-sm">
                            <i class="fas fa-box text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">Aucune livraison en cours</p>
                            <button onclick="window.location.href='{{ url('commnandes/create') }}'" 
                                    class="mt-3 text-red-600 hover:text-red-800 font-medium">
                                ➕ Créer une livraison
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Livreurs disponibles -->
            <div class="p-4">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-bold text-lg flex items-center">
                        <i class="fas fa-users text-red-600 mr-2"></i> Livreurs disponibles
                    </h2>
                    <span class="text-sm bg-red-100 text-red-800 px-2 py-1 rounded-full" id="livreursCount">
                        {{ isset($livreursDisponibles) ? count($livreursDisponibles) : 0 }} en ligne
                    </span>
                </div>
                
                <div id="livreursDisponibles" class="grid grid-cols-2 gap-4">
                    @if(isset($livreursDisponibles) && $livreursDisponibles->count() > 0)
                        @foreach($livreursDisponibles as $livreur)
                            @include('client.partials.livreur-card', ['livreur' => $livreur])
                        @endforeach
                    @else
                        <div class="col-span-2 text-center py-6">
                            <i class="fas fa-users-slash text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">Aucun livreur disponible pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="p-4 border-t mt-2">
                <h3 class="font-bold mb-3 flex items-center">
                    <i class="fas fa-chart-line text-red-600 mr-2"></i> Vos statistiques
                </h3>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-white border rounded-xl p-3 shadow-sm">
                        <div class="text-xl font-bold text-red-600">{{ $statistiques['total_commandes'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Commandes</div>
                    </div>
                    <div class="bg-white border rounded-xl p-3 shadow-sm">
                        <div class="text-xl font-bold text-yellow-500">{{ isset($statistiques['note_moyenne']) ? number_format($statistiques['note_moyenne'], 1) : '0.0' }}</div>
                        <div class="text-xs text-gray-500">Note moyenne</div>
                    </div>
                    <div class="bg-white border rounded-xl p-3 shadow-sm">
                        <div class="text-xl font-bold text-red-600">{{ isset($statistiques['montant_total']) ? number_format($statistiques['montant_total']) : 0 }}</div>
                        <div class="text-xs text-gray-500">FCFA dépensés</div>
                    </div>
                </div>
            </div>

            <!-- Livraisons terminées à noter -->
            <div class="p-4 border-t mt-4">
                <h3 class="font-bold mb-3 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i> Livraisons terminées
                    @if(isset($livraisonsTerminees))
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                            {{ count($livraisonsTerminees) }}
                        </span>
                    @endif
                </h3>

                @if(isset($livraisonsTerminees) && count($livraisonsTerminees) > 0)
                    @foreach($livraisonsTerminees as $livraison)
                        <div class="bg-white shadow p-5 rounded-lg mb-4 border">
                            <!-- En-tête de la livraison -->
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-box mr-2 text-blue-500"></i>
                                        Livraison #{{ $livraison->id }}
                                    </h4>
                                    <p class="text-gray-600 mt-1">
                                        <i class="fas fa-route mr-1 text-red-500"></i>
                                        {{ Str::limit($livraison->adresse_depart, 25) }} 
                                        → {{ Str::limit($livraison->adresse_arrivee, 25) }}
                                    </p>
                                    @if($livraison->driver)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-user mr-1 text-orange-500"></i>
                                            Livreur : <strong>{{ $livraison->driver->name }}</strong>
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-user mr-1 text-orange-500"></i>
                                            Livreur : Non disponible
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar mr-1"></i>
                                        Livré le {{ $livraison->updated_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ number_format($livraison->prix_final) }} FCFA
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">{{ $livraison->type_colis }}</div>
                                </div>
                            </div>

                            <!-- Vérifier l'évaluation -->
                            @if($livraison->evaluation)
                                <div class="bg-red-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-red-700 font-semibold flex items-center">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Évaluation envoyée
                                        </p>
                                        <span class="text-xs text-gray-500">
                                            {{ $livraison->evaluation->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <span class="text-sm text-gray-600 mr-2">Votre note :</span>
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $livraison->evaluation->note ? 'text-yellow-400' : 'text-gray-300' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.785.57-1.84-.197-1.54-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-2 text-sm font-medium text-gray-700">
                                            {{ $livraison->evaluation->note }}/5
                                        </span>
                                    </div>
                                    @if($livraison->evaluation->commentaire)
                                        <div class="mt-2 p-2 bg-white rounded border">
                                            <p class="text-sm text-gray-600 italic">
                                                "{{ $livraison->evaluation->commentaire }}"
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if($livraison->driver_id)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <p class="text-blue-800 font-medium mb-3 flex items-center">
                                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                                            Comment s'est passée votre livraison ?
                                        </p>
                                        <form action="{{ route('client.evaluations') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="commande_id" value="{{ $livraison->id }}">

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">
                                                    Votre note *
                                                </label>
                                                <div class="flex items-center space-x-1" id="rating-{{ $livraison->id }}">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <label class="cursor-pointer group">
                                                            <input type="radio" name="rating" value="{{ $i }}" 
                                                                   class="hidden peer" required 
                                                                   onchange="updateStars({{ $livraison->id }}, {{ $i }})">
                                                            <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 group-hover:text-yellow-500 transition-all duration-200 star-{{ $livraison->id }}"
                                                                 fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3 .921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.785 .57-1.84-.197-1.54-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81 .588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/>
                                                            </svg>
                                                        </label>
                                                    @endfor
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">
                                                    Votre commentaire (optionnel)
                                                </label>
                                                <textarea name="commentaire" rows="3" 
                                                          placeholder="Dites-nous comment s'est passée votre livraison..."
                                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"></textarea>
                                            </div>

                                            <div class="flex justify-end">
                                                <button type="submit" 
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center">
                                                    <i class="fas fa-paper-plane mr-2"></i>
                                                    Envoyer mon évaluation
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                                        <p class="text-gray-600">
                                            Impossible d'évaluer cette livraison
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Aucun livreur n'a été assigné à cette commande
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-gray-50 border border-gray-200 p-8 rounded-xl text-center">
                        <div class="mb-4">
                            <i class="fas fa-box-open text-gray-400 text-4xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-600 mb-2">
                            Aucune livraison terminée
                        </h4>
                        <p class="text-gray-500 mb-4">
                            Vos livraisons terminées apparaîtront ici pour pouvoir les noter
                        </p>
                        <button onclick="window.location.href='{{ url('commnandes/create') }}'" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Créer une livraison
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- === ONGLET PROFIL === -->
        <div id="profileTab" class="tab-content hidden p-4">
            <h2 class="font-bold text-xl mb-4 flex items-center">
                @if(Auth::user()->genre == 'F')
                    <i class="fas fa-female text-pink-500 mr-2"></i>
                @elseif(Auth::user()->genre == 'M')
                    <i class="fas fa-male text-blue-500 mr-2"></i>
                @else
                    <i class="fas fa-user text-gray-500 mr-2"></i>
                @endif
                Mon Profil
            </h2>

            <!-- Infos utilisateur -->
            <div class="flex items-center mb-6">
                <div class="rounded-full w-16 h-16 mr-4 flex items-center justify-center
                    @if(Auth::user()->genre == 'F') bg-pink-100 text-pink-500
                    @elseif(Auth::user()->genre == 'M') bg-blue-100 text-blue-500
                    @else bg-purple-100 text-purple-500 @endif">
                    <i class="fas fa-user text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">
                        {{ Auth::user()->name ?? (Auth::user()->prenom . ' ' . Auth::user()->nom) }}
                    </h3>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-500">{{ Auth::user()->numero_telephone ?? 'Non renseigné' }}</p>
                </div>
            </div>

            <!-- Menu profil -->
            <div class="space-y-3">
                <a href="{{ route('profile.edit') }}" class="menu-item">
                    <i class="fas fa-edit bg-blue-100 text-blue-600"></i>
                    <span>Modifier mon profil</span>
                </a>

                <a href="{{ route('client.aide') }}" class="menu-item">
                    <i class="fas fa-question-circle bg-yellow-100 text-yellow-600"></i>
                    <span>Aide & Support</span>
                </a>

                <a href="{{ route('user.messages') }}" class="menu-item">
                    <i class="fas fa-envelope bg-orange-100 text-orange-600"></i>
                    <span>Voir mes messages</span>
                </a>

                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="menu-item">
                    <i class="fas fa-sign-out-alt bg-red-100 text-red-600"></i>
                    <span>Déconnexion</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </div>
</div>

<!-- Barre navigation -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t max-w-md mx-auto flex justify-around py-3 rounded-t-xl shadow-md">
    <button onclick="showTab('homeTab')" class="tab-btn text-red-600">
        <i class="fas fa-home block text-xl"></i>
        <span class="text-xs">Accueil</span>
    </button>

    <a href="{{ route('tokens.index') }}" class="tab-btn text-gray-500 hover:text-red-600">
        <i class="fas fa-coins block text-xl"></i>
        <span class="text-xs">Jetons</span>
    </a>

    <a href="{{ route('commnandes.index') }}" class="tab-btn text-gray-500 hover:text-red-600">
        <i class="fas fa-history block text-xl"></i>
        <span class="text-xs">Historique</span>
    </a>

    <button onclick="showTab('profileTab')" class="tab-btn text-gray-500 hover:text-red-600">
        <i class="fas fa-user block text-xl"></i>
        <span class="text-xs">Profil</span>
    </button>
</div>

<style>
    .menu-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        transition: 0.2s;
    }
    .menu-item:hover {
        background: #f9fafb;
    }
    .menu-item i {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 12px;
    }
    .tab-btn.active {
        color: #dc2626;
    }
</style>

<script>
    
    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active', 'text-red-600'));
        event.currentTarget.classList.add('active', 'text-red-600');
    }

    function refreshDashboardData() {
        fetch('/api/dashboard-data')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateCommandeEnCours(data.data.commande_en_cours);
                    updateLivreursDisponibles(data.data.livreurs_disponibles);
                }
            });
    }

    function updateCommandeEnCours(commande) { /* reste identique */ }
    function updateLivreursDisponibles(livreurs) { /* reste identique */ }

    function updateStars(commandeId, rating) {
        const stars = document.querySelectorAll(`.star-${commandeId}`);
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        showTab('homeTab');
        setInterval(refreshDashboardData, 30000);
    });
</script>
@endsection
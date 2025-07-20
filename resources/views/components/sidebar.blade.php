<aside class="hidden md:flex md:flex-col md:w-64 bg-gradient-to-b from-rose-500 to-rose-700 text-white shadow-lg">
    <!-- Logo -->
    <div class="p-6 flex justify-center">
        <img src="{{ asset('image/fast.jpg') }}" alt="ColisFast Logo" class="h-32 w-auto object-contain">
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-2">

        <!-- Tableau de bord -->
        <a href="{{ route('livreur.dashboarde') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.dashboarde') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-home w-6"></i>
            <span>Tableau de bord</span>
        </a>

        <!-- Livraisons disponibles -->
        <a href="{{ route('livreur.livraisons-disponible') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.livraisons-disponible') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-list w-6"></i>
            <span>Livraisons disponibles</span>
        </a>

        <!-- Livraisons en cours -->
        <a href="{{ route('livreur.livraison-cours') }}"
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livraison-cours') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-truck w-6"></i>
            <span>Livraisons en cours</span>
        </a>

        <!-- ✅ Nouveau lien : Déclarer un trajet urbain -->
<a href="{{ route('livreur.trajets.index') }}"
   class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.trajet.urbain') ? 'bg-rose-600' : '' }}">
    <i class="fas fa-route w-6"></i>
    <span>Déclarer un trajet urbain</span>
</a>

        <!-- Mes revenus -->
        <a href="{{ route('livreur.revenus') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.revenus') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-wallet w-6"></i>
            <span>Mes revenus</span>
        </a>

        <!-- Statistiques -->
        <a href="{{ route('statistiques.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('statistiques.index') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-chart-bar w-6"></i>
            <span>Statistiques</span>
        </a>

        <!-- Déconnexion -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" 
                    class="flex items-center w-full px-4 py-3 rounded-lg text-white hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt w-6"></i>
                <span>Déconnexion</span>
            </button>
        </form>

    </nav>
</aside>


<!-- Mobile Sidebar Toggle -->
<div class="md:hidden fixed bottom-0 inset-x-0 bg-rose-600 text-white z-50">
    <div class="flex justify-around">
        <a href="{{ url('/livreur/dashboard') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/dashboard') ? 'bg-rose-700' : '' }}">
            <i class="fas fa-home text-lg"></i>
            <span class="text-xs">Accueil</span>
        </a>
        <a href="{{ url('/livreur/available-deliveries') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/available-deliveries') ? 'bg-rose-700' : '' }}">
            <i class="fas fa-list text-lg"></i>
            <span class="text-xs">Livraisons</span>
        </a>
        <a href="{{ url('/livreur/ongoing-deliveries') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/ongoing-deliveries') ? 'bg-rose-700' : '' }}">
            <i class="fas fa-truck text-lg"></i>
            <span class="text-xs">En cours</span>
        </a>
        <a href="{{ url('/livreur/navigation') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/navigation') ? 'bg-rose-700' : '' }}">
            <i class="fas fa-map-marker-alt text-lg"></i>
            <span class="text-xs">GPS</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('profile/edit') ? 'bg-rose-700' : '' }}">
    <i class="fas fa-user text-lg"></i>
    <span class="text-xs">Profil</span>
</a>

    </div>
</div>

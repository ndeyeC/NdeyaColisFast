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
            <i class="fas fa-home w-6 mr-3"></i>
            <span>Tableau de bord</span>
        </a>

        <!-- Livraisons disponibles -->
        <a href="{{ route('livreur.livraisons-disponible') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.livraisons-disponible') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-list w-6 mr-3"></i>
            <span>Livraisons disponibles</span>
        </a>

        <!-- Livraisons en cours -->
        <a href="{{ route('livreur.livraison-cours') }}"
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livraison-cours') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-truck w-6 mr-3"></i>
            <span>Livraisons en cours</span>
        </a>

        <!-- Déclarer un trajet urbain -->
        <a href="{{ route('livreur.trajets.index') }}"
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.trajet.urbain') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-route w-6 mr-3"></i>
            <span>Déclarer un trajet urbain</span>
        </a>

        <!-- Mes revenus -->
        <a href="{{ route('livreur.revenus') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.revenus') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-wallet w-6 mr-3"></i>
            <span>Mes revenus</span>
        </a>

        <!-- Statistiques -->
        <a href="{{ route('statistiques.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('statistiques.index') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-chart-bar w-6 mr-3"></i>
            <span>Statistiques</span>
        </a>

        <!-- Déconnexion -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" 
                    class="flex items-center w-full px-4 py-3 rounded-lg text-white hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                <span>Déconnexion</span>
            </button>
        </form>

    </nav>
</aside>
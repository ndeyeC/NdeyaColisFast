<aside class="hidden md:flex md:flex-col md:w-64 bg-gradient-to-b from-rose-500 to-rose-700 text-white shadow-lg">
    <div class="p-6">
        <div class="flex items-center justify-center">
       <img src="{{ asset('image/fast.jpg') }}" alt="ColisFast Logo" class="h-40 w-auto max-w-full object-contain">
        </div>
    </div>

    <!-- <nav class="flex-1 px-2 py-4 space-y-2">
        <a href="{{ route('livreur.dashboarde') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->is('livreur/dashboard') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-home w-6"></i>
            <span>Tableau de bord</span>
        </a> 
</nav> -->

    <nav class="flex-1 px-2 py-4 space-y-2">
    <a href="{{ route('livreur.dashboarde') }}" 
       class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->is('livreur/dashboarde') ? 'bg-rose-600' : '' }}">
        <i class="fas fa-home w-6"></i>
        <span>Tableau de bord</span>
    </a>
</nav>
     <a href="{{ route('livreur.livraisons-disponible') }}" 
   class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.livraisons-disponible') ? 'bg-rose-600' : '' }}">
    <i class="fas fa-list w-6"></i>
    <span>Livraisons disponibles</span>
</a>

      <a href="{{ route('livreur.livraison-cours') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.livraison-cours') ? 'bg-rose-600' : '' }}">
    <i class="fas fa-truck w-6"></i>
    <span>Livraisons en cours</span>
</a>


        <a href="{{ route('livreur.navigation') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.navigation') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-map-marker-alt w-6"></i>
            <span>Navigation GPS</span>
        </a> 

       


        <a href="{{ route('livreur.revenus') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->is('livreur/earnings') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-wallet w-6"></i>
            <span>Mes revenus</span>
        </a>

        <a href="{{ route('statistiques.index') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.statistics') ? 'bg-rose-600' : '' }}">
            <i class="fas fa-chart-bar w-6"></i>
            <span>Statistiques</span>
        </a>

        <!-- Déconnexion -->
        <a href="{{ route('logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-red-600 transition-colors">
            <i class="fas fa-sign-out-alt w-6"></i>
            <span>Déconnexion</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </nav>

    <div class="p-4 border-t border-rose-600">
        <a href="{{ url('/logout') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors">
            <i class="fas fa-sign-out-alt w-6"></i>
            <span>Déconnexion</span>
        </a>
    </div>
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
        <a href="{{ url('/livreur/profile') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/profile') ? 'bg-rose-700' : '' }}">
            <i class="fas fa-user text-lg"></i>
            <span class="text-xs">Profil</span>
        </a>
    </div>
</div>

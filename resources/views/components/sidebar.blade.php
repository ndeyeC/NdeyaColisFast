<aside class="hidden md:flex md:flex-col md:w-64 bg-gradient-to-b from-green-500 to-green-700 text-white shadow-lg">
    <div class="p-6">
        <div class="flex items-center justify-center">
            <img src="{{ asset('image/logo.png') }}" alt="ColisFast Logo" class="h-80, w-70">
            <!-- <h1 class="ml-2 text-xl font-bold">ColisFast</h1> -->
        </div>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-2">
        <a href="{{ route('livreur.dashboarde') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-green-600 transition-colors {{ request()->is('livreur/dashboard') ? 'bg-green-600' : '' }}">
            <i class="fas fa-home w-6"></i>
            <span>Tableau de bord</span>
        </a>
        
        <a href="{{ route('livreur.livraisons-disponible') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-green-600 transition-colors {{ request()->routeIs('livreur.available-deliveries') ? 'bg-green-600' : '' }}">
    <i class="fas fa-list w-6"></i>
    <span>Livraisons disponibles</span>
</a>

        
<a href="{{ route('livreur.livraison-cours') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-green-600 transition-colors {{ request()->routeIs('livreur.ongoing-deliveries') ? 'bg-green-600' : '' }}">
    <i class="fas fa-truck w-6"></i>
    <span>Livraisons en cours</span>
</a>

        
<a href="{{ route('livreur.navigation') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-green-600 transition-colors {{ request()->routeIs('livreur.navigation') ? 'bg-green-600' : '' }}">
    <i class="fas fa-map-marker-alt w-6"></i>
    <span>Navigation GPS</span>
</a>

        
        <a href="{{ route('livreur.revenus') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-green-600 transition-colors {{ request()->is('livreur/earnings') ? 'bg-green-600' : '' }}">
            <i class="fas fa-wallet w-6"></i>
            <span>Mes revenus</span>
        </a>
        
        <a href="{{ route('livreur.statistics') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-green-600 transition-colors {{ request()->routeIs('livreur.statistics') ? 'bg-green-600' : '' }}">
    <i class="fas fa-chart-bar w-6"></i>
    <span>Statistiques</span>
</a>

        
      <!-- Bouton de déconnexion avec le même style -->
<a href="{{ route('logout') }}" 
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
   class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-red-600 transition-colors">
    <i class="fas fa-sign-out-alt w-6"></i>
    <span>Déconnexion</span>
</a>

<!-- Formulaire caché pour la déconnexion -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

    </nav>

    <div class="p-4 border-t border-green-600">
        <a href="{{ url('/logout') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-green-600 transition-colors">
            <i class="fas fa-sign-out-alt w-6"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>

<!-- Mobile Sidebar Toggle -->
<div class="md:hidden fixed bottom-0 inset-x-0 bg-green-600 text-white z-50">
    <div class="flex justify-around">
        <a href="{{ url('/livreur/dashboard') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/dashboard') ? 'bg-green-700' : '' }}">
            <i class="fas fa-home text-lg"></i>
            <span class="text-xs">Accueil</span>
        </a>
        <a href="{{ url('/livreur/available-deliveries') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/available-deliveries') ? 'bg-green-700' : '' }}">
            <i class="fas fa-list text-lg"></i>
            <span class="text-xs">Livraisons</span>
        </a>
        <a href="{{ url('/livreur/ongoing-deliveries') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/ongoing-deliveries') ? 'bg-green-700' : '' }}">
            <i class="fas fa-truck text-lg"></i>
            <span class="text-xs">En cours</span>
        </a>
        <a href="{{ url('/livreur/navigation') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/navigation') ? 'bg-green-700' : '' }}">
            <i class="fas fa-map-marker-alt text-lg"></i>
            <span class="text-xs">GPS</span>
        </a>
        <a href="{{ url('/livreur/profile') }}" class="flex flex-col items-center py-2 flex-1 {{ request()->is('livreur/profile') ? 'bg-green-700' : '' }}">
            <i class="fas fa-user text-lg"></i>
            <span class="text-xs">Profil</span>
        </a>
    </div>
</div>
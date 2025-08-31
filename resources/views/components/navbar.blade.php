<header class="bg-white shadow-sm z-10">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Mobile Menu Button -->
        <button class="md:hidden text-gray-500 focus:outline-none" 
                type="button" 
                id="mobile-menu-button"
                onclick="toggleMobileMenu()">
            <i class="fas fa-bars text-xl"></i>
        </button>
        
        <!-- Page Title -->
        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Tableau de bord')</h1>
        
        <!-- Right Side Navigation -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
            </div>
            
           <!-- User Profile Dropdown -->
       <div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
        <img src="{{ asset('images/avatar-placeholder.png') }}" alt="Profil" class="w-8 h-8 rounded-full border border-gray-300">
        <span class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
    </button>

    <!-- Profile Dropdown -->
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            <i class="fas fa-user mr-2 text-gray-500"></i> Mon profil
        </a>
        <div class="border-t border-gray-100"></div>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>

        </div>
    </div>
</header>

<!-- Mobile Sidebar Overlay -->
<div id="mobile-menu-overlay" 
     class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden hidden"
     onclick="closeMobileMenu()"></div>

<!-- Mobile Sidebar -->
<aside id="mobile-sidebar" 
       class="fixed left-0 top-0 h-full w-64 bg-gradient-to-b from-rose-500 to-rose-700 text-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 md:hidden">
    
    <!-- Close button -->
    <div class="flex justify-between items-center p-4 border-b border-rose-400">
        <h2 class="text-lg font-semibold">Menu</h2>
        <button onclick="closeMobileMenu()" class="text-white hover:text-gray-200">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <!-- Logo -->
    <div class="p-6 flex justify-center">
        <img src="{{ asset('image/fast.jpg') }}" alt="ColisFast Logo" class="h-24 w-auto object-contain">
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">

        <!-- Tableau de bord -->
        <a href="{{ route('livreur.dashboarde') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.dashboarde') ? 'bg-rose-600' : '' }}"
           onclick="closeMobileMenu()">
            <i class="fas fa-home w-6 mr-3"></i>
            <span>Tableau de bord</span>
        </a>

        <!-- Livraisons disponibles -->
        <a href="{{ route('livreur.livraisons-disponible') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.livraisons-disponible') ? 'bg-rose-600' : '' }}"
           onclick="closeMobileMenu()">
            <i class="fas fa-list w-6 mr-3"></i>
            <span>Livraisons disponibles</span>
        </a>

        <!-- Livraisons en cours -->
        <a href="{{ route('livreur.livraison-cours') }}"
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livraison-cours') ? 'bg-rose-600' : '' }}"
           onclick="closeMobileMenu()">
            <i class="fas fa-truck w-6 mr-3"></i>
            <span>Livraisons en cours</span>
        </a>

        <!-- Déclarer un trajet urbain -->
        <a href="{{ route('livreur.trajets.index') }}"
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.trajet.urbain') ? 'bg-rose-600' : '' }}"
           onclick="closeMobileMenu()">
            <i class="fas fa-route w-6 mr-3"></i>
            <span>Déclarer un trajet urbain</span>
        </a>

        <!-- Mes revenus -->
        <a href="{{ route('livreur.revenus') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('livreur.revenus') ? 'bg-rose-600' : '' }}"
           onclick="closeMobileMenu()">
            <i class="fas fa-wallet w-6 mr-3"></i>
            <span>Mes revenus</span>
        </a>

        <!-- Statistiques -->
        <a href="{{ route('statistiques.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-rose-600 transition-colors {{ request()->routeIs('statistiques.index') ? 'bg-rose-600' : '' }}"
           onclick="closeMobileMenu()">
            <i class="fas fa-chart-bar w-6 mr-3"></i>
            <span>Statistiques</span>
        </a>

        <!-- Déconnexion -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" 
                    class="flex items-center w-full px-4 py-3 rounded-lg text-white hover:bg-red-600 transition-colors"
                    onclick="closeMobileMenu()">
                <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                <span>Déconnexion</span>
            </button>
        </form>

    </nav>
</aside>
<script src="{{ asset('js/mobile-menu.js') }}"></script>
<script src="{{ asset('js/navbar-scroll.js') }}"></script>
<script src="{{ asset('js/smooth-scroll.js') }}"></script>
<script src="{{ asset('js/card-animation.js') }}"></script>
<script src="{{ asset('js/counter-animation.js') }}"></script>

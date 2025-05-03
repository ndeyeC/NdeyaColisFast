<header class="bg-white shadow-sm z-10">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Mobile Menu Button -->
        <button class="md:hidden text-gray-500 focus:outline-none" type="button">
            <i class="fas fa-bars text-xl"></i>
        </button>
        
        <!-- Page Title -->
        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Tableau de bord')</h1>
        
        <!-- Right Side Navigation -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">3</span>
                </button>
                
                <!-- Notification Panel -->
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-700">Notifications</p>
                    </div>
                    
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">
                        <p class="text-sm font-medium text-gray-900">Nouvelle livraison disponible</p>
                        <p class="text-xs text-gray-500">Il y a 5 minutes</p>
                    </a>
                    
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">
                        <p class="text-sm font-medium text-gray-900">Félicitations! Vous avez reçu un pourboire</p>
                        <p class="text-xs text-gray-500">Il y a 1 heure</p>
                    </a>
                    
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">
                        <p class="text-sm font-medium text-gray-900">Mise à jour de l'application</p>
                        <p class="text-xs text-gray-500">Il y a 1 jour</p>
                    </a>
                    
                    <div class="px-4 py-2 border-t border-gray-100">
                        <a href="#" class="text-sm text-green-600 hover:text-green-700">Voir toutes les notifications</a>
                    </div>
                </div>
            </div>
            
            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                    <img src="{{ asset('images/avatar-placeholder.png') }}" alt="Profile" class="w-8 h-8 rounded-full border border-gray-300">
                    <span class="hidden md:block text-sm font-medium text-gray-700">Amadou Diallo</span>
                    <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                </button>
                
                <!-- Profile Dropdown -->
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                    <a href="{{ url('/livreur/profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2 text-gray-500"></i> Mon profil
                    </a>
                    <a href="{{ url('/livreur/settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2 text-gray-500"></i> Paramètres
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-question-circle mr-2 text-gray-500"></i> Aide
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <a href="{{ url('/logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
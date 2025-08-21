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
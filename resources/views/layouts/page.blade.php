<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'colisFast - Livraison rapide au Sénégal')</title>
    <meta name="description" content="@yield('description', 'La solution de livraison innovante au Sénégal. Rapide, fiable et économique.')">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header simplifié pour les pages publiques -->
    <header class="bg-white shadow-lg">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="text-3xl font-black">
                        <span class="text-red-500">colis</span><span class="text-red-400">Fast</span>
                    </a>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="/services/livraison-express" class="text-gray-700 hover:text-red-600 transition-colors">Services</a>
                    <a href="/apropos" class="text-gray-700 hover:text-red-600 transition-colors">À propos</a>
                    <a href="/blog" class="text-gray-700 hover:text-red-600 transition-colors">Blog</a>
                    <a href="/contact" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">Contact</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenu principal -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-6">
                    <div class="text-3xl font-black">
                        <span class="text-red-500">colis</span><span class="text-red-400">Fast</span>
                    </div>
                    <p class="text-gray-400 leading-relaxed">La solution de livraison innovante au Sénégal. Rapide, fiable et économique.</p>
                    <div class="flex space-x-4">
                        <a href="https://facebook.com/colisfast" target="_blank" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/colisfast" target="_blank" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://instagram.com/colisfast" target="_blank" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://linkedin.com/company/colisfast" target="_blank" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Services</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('services.livraison-express') }}" class="text-gray-300 hover:text-red-400 transition-colors">Livraison express</a></li>
                        <li><a href="{{ route('services.livraison-programmee') }}" class="text-gray-300 hover:text-red-400 transition-colors">Livraison programmée</a></li>
                        <li><a href="{{ route('services.solutions-ecommerce') }}" class="text-gray-300 hover:text-red-400 transition-colors">Solutions e-commerce</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Entreprise</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('apropos') }}" class="text-gray-300 hover:text-red-400 transition-colors">À propos</a></li>
                        <li><a href="{{ route('carrieres') }}" class="text-gray-300 hover:text-red-400 transition-colors">Carrières</a></li>
                        <li><a href="{{ route('blog') }}" class="text-gray-300 hover:text-red-400 transition-colors">Blog</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Légal</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('legal.conditions') }}" class="text-gray-300 hover:text-red-400 transition-colors">Conditions d'utilisation</a></li>
                        <li><a href="{{ route('legal.confidentialite') }}" class="text-gray-300 hover:text-red-400 transition-colors">Politique de confidentialité</a></li>
                        <li><a href="{{ route('legal.mentions') }}" class="text-gray-300 hover:text-red-400 transition-colors">Mentions légales</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-800">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">© 2025 colisFast. Tous droits réservés.</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="{{ route('legal.cookies') }}" class="text-gray-400 hover:text-red-400 text-sm transition-colors">Politique de cookies</a>
                        <a href="{{ route('sitemap') }}" class="text-gray-400 hover:text-red-400 text-sm transition-colors">Plan du site</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
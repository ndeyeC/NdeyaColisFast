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
                
            </div>
        </nav>
    </header>

    <!-- Contenu principal -->
    <main class="min-h-screen">
        @yield('content')
    </main>

   
    @yield('scripts')
</body>
</html>
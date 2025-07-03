<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>colisFast | Livraison rapide au Sénégal</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
       .hero-pattern {
    background: linear-gradient(135deg, #b03a2e 0%, #e06666 100%);
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <!-- <span class="text-blue-600 text-2xl font-bold">colis<span class="text-blue-900">Fast</span></span> -->
             <span class="text-2xl font-extrabold tracking-tight">
                    <span class="text-red-600">Colis</span><span class="text-red-800 italic">Fast</span>
             </span>

                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                       <a href="#" class="border-red-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                     Accueil
                    </a>

                        <a href="#comment-ca-marche" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Comment ça marche
                        </a>
                        <a href="#avantages" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Avantages
                        </a>
                        <a href="#contact" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Contact
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Connexion</a>
           <a href="{{ route('register') }}" 
            class="ml-4 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                Inscription
        </a>
                </div>
                <div class="-mr-2 flex items-center md:hidden">
                    <button type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-expanded="false">
                        <span class="sr-only">Ouvrir le menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>


    <!-- Hero Section -->
<div class="relative overflow-hidden">
    <!-- Image d'arrière-plan avec overlay -->
    <div class="absolute inset-0 z-0">
        <img src="image/logo.png" 
             alt="Livreur en moto" class="w-full h-full object-cover">
         <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-blue-600 opacity-80"></div> 
    </div>
    
    <!-- Contenu du hero -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="text-white">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">
                    La livraison n'a jamais été aussi simple au Sénégal
                </h1>
                <p class="mt-4 text-xl text-blue-100">
                    Connectez-vous avec des livreurs professionnels et faites livrer vos colis rapidement, en toute sécurité et à prix abordable.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                   
                

                </div>
            </div>
            <div class="hidden md:block">
                <!-- <img src="https://img.freepik.com/free-vector/delivery-service-illustrated_23-2148505081.jpg" alt="Service de livraison" class="h-auto w-full rounded-lg shadow-2xl transform -rotate-3 hover:rotate-0 transition duration-500"> -->
            </div>
        </div>
    </div>
</div>

    <!-- Stats Section -->
 <!-- Statistiques Section -->
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="p-6 bg-red-50 rounded-lg">
                <div class="text-red-600 text-4xl mb-2">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="text-5xl font-bold text-gray-900">15min</div>
                <p class="mt-2 text-lg text-gray-600">Temps de livraison moyen en zone urbaine</p>
            </div>
            <div class="p-6 bg-red-50 rounded-lg">
                <div class="text-red-600 text-4xl mb-2">
                    <i class="fas fa-users"></i>
                </div>
                <div class="text-5xl font-bold text-gray-900">100+</div>
                <p class="mt-2 text-lg text-gray-600">Livreurs partenaires dans tout le Sénégal</p>
            </div>
            <div class="p-6 bg-red-50 rounded-lg">
                <div class="text-red-600 text-4xl mb-2">
                    <i class="fas fa-star"></i>
                </div>
                <div class="text-5xl font-bold text-gray-900">4.8/5</div>
                <p class="mt-2 text-lg text-gray-600">Note moyenne de satisfaction client</p>
            </div>
        </div>
    </div>
</div>

<!-- Comment ça marche Section -->
<div id="comment-ca-marche" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Comment ça marche
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Un processus simple en trois étapes pour des livraisons sans tracas
            </p>
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="mx-auto h-20 w-20 rounded-full bg-red-600 flex items-center justify-center text-white text-3xl">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="mt-6 text-xl font-medium text-gray-900">1. Commandez</h3>
                <p class="mt-2 text-base text-gray-500">
                    Inscrivez-vous sur notre plateforme et passez votre commande de livraison en quelques clics.
                </p>
            </div>

            <div class="text-center">
                <div class="mx-auto h-20 w-20 rounded-full bg-red-600 flex items-center justify-center text-white text-3xl">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <h3 class="mt-6 text-xl font-medium text-gray-900">2. Suivez</h3>
                <p class="mt-2 text-base text-gray-500">
                    Un livreur proche prend en charge votre colis et vous pouvez suivre son trajet en temps réel.
                </p>
            </div>

            <div class="text-center">
                <div class="mx-auto h-20 w-20 rounded-full bg-red-600 flex items-center justify-center text-white text-3xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="mt-6 text-xl font-medium text-gray-900">3. Recevez</h3>
                <p class="mt-2 text-base text-gray-500">
                    Votre destinataire reçoit le colis rapidement et vous êtes notifié de la livraison.
                </p>
            </div>
        </div>
    </div>
</div>


    <!-- Benefits Section -->
<div id="avantages" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Pourquoi choisir colisFast ?
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Des avantages exclusifs pour révolutionner vos livraisons
            </p>
        </div>

        <div class="mt-16">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-3 lg:gap-8">
                <div class="bg-red-50 p-8 rounded-lg">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-tachometer-alt text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Livraison Ultra Rapide</h3>
                    <p class="mt-4 text-gray-500">
                        Notre réseau de livreurs couvre toutes les zones urbaines du Sénégal pour des livraisons en un temps record.
                    </p>
                </div>

                <div class="bg-red-50 p-8 rounded-lg">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-wallet text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Tarifs Compétitifs</h3>
                    <p class="mt-4 text-gray-500">
                        Profitez de prix abordables grâce à notre système de paiement par jetons et notre optimisation des trajets.
                    </p>
                </div>

                <div class="bg-red-50 p-8 rounded-lg">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-shield-alt text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Sécurité Garantie</h3>
                    <p class="mt-4 text-gray-500">
                        Tous nos livreurs sont vérifiés et vous pouvez suivre votre livraison en temps réel pour plus de tranquillité.
                    </p>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-12 lg:grid-cols-3 lg:gap-8">
                <div class="bg-red-50 p-8 rounded-lg">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-map-marked-alt text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Couverture Nationale</h3>
                    <p class="mt-4 text-gray-500">
                        Nous livrons dans toutes les grandes villes du Sénégal et continuons d'étendre notre réseau.
                    </p>
                </div>

                <div class="bg-red-50 p-8 rounded-lg">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-headset text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Support Client 24/7</h3>
                    <p class="mt-4 text-gray-500">
                        Notre équipe est disponible à tout moment pour répondre à vos questions et résoudre vos problèmes.
                    </p>
                </div>

                <div class="bg-red-50 p-8 rounded-lg">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-mobile-alt text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Application Intuitive</h3>
                    <p class="mt-4 text-gray-500">
                        Interface simple et conviviale pour commander une livraison en quelques clics seulement.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>



    <!-- Contact Section -->
    <div id="contact" class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Contactez-nous</h2>
            <p class="mt-4 text-lg text-gray-500">
                Une question ? Notre équipe est là pour vous aider
            </p>
        </div>
        <div class="mt-12 max-w-lg mx-auto grid grid-cols-1 gap-8 md:grid-cols-3">
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-center">
                    <i class="fas fa-phone text-red-600 text-3xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Téléphone</h3>
                    <p class="mt-2 text-base text-gray-500">+221 774836026</p>
                </div>
            </div>
    <div class="bg-white shadow overflow-hidden rounded-lg">
              <div class="px-4 py-5 sm:p-6 text-center break-words">
          <i class="fas fa-envelope text-red-600 text-3xl mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900">Email</h3>
          <p class="mt-2 text-base text-gray-500">ColisFast293@gmail.com</p>
         </div>
       </div>

            <div class="bg-white shadow overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-center">
                    <i class="fas fa-map-marker-alt text-red-600 text-3xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Adresse</h3>
                    <p class="mt-2 text-base text-gray-500">Dakar</p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
          <div class="text-2xl font-bold text-white">
             <span class="text-red-500">colis</span><span class="text-red-400">Fast</span>
     </div>
                <p class="mt-4 text-gray-300">
                    La solution de livraison innovante au Sénégal. Rapide, fiable et économique.
                </p>
                <div class="mt-6 flex space-x-6">
                    <a href="https://facebook.com/colisfast" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/colisfast" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.com/colisfast" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://linkedin.com/company/colisfast" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Services</h3>
                <ul class="mt-4 space-y-4">
                    <li><a href="/services/livraison-express" class="text-base text-gray-300 hover:text-white">Livraison express</a></li>
                    <li><a href="/services/livraison-programmee" class="text-base text-gray-300 hover:text-white">Livraison programmée</a></li>
                    <li><a href="/services/solutions-ecommerce" class="text-base text-gray-300 hover:text-white">Solutions e-commerce</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Entreprise</h3>
                <ul class="mt-4 space-y-4">
                    <li><a href="/apropos" class="text-base text-gray-300 hover:text-white">À propos</a></li>
                    <li><a href="/carrieres" class="text-base text-gray-300 hover:text-white">Carrières</a></li>
                    <li><a href="/blog" class="text-base text-gray-300 hover:text-white">Blog</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Légal</h3>
                <ul class="mt-4 space-y-4">
                    <li><a href="/legal/conditions" class="text-base text-gray-300 hover:text-white">Conditions d'utilisation</a></li>
                    <li><a href="/legal/confidentialite" class="text-base text-gray-300 hover:text-white">Politique de confidentialité</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-12 border-t border-gray-700 pt-8">
            <p class="text-base text-gray-400 text-center">
                &copy; 2025 colisFast. Tous droits réservés.
            </p>
        </div>
    </div>
</footer>
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('button[aria-expanded]');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !expanded);
                    
                    // Your mobile menu toggle logic here
                    // For a simple implementation, you'd add code to show/hide a mobile menu
                });
            }
        });
    </script>
</body>
</html>
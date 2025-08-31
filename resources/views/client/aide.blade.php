@extends('layouts.template')

@section('title', 'Aide & Support')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-md rounded-2xl p-6">

    <!-- 🔙 Bouton retour vers le Dashboard -->
<a href="{{ route('dashboard') }}" 
   class="absolute top-3 left-3 flex items-center px-3 py-1.5 
          bg-gray-100 hover:bg-red-50 border border-gray-200 rounded-lg 
          text-gray-700 hover:text-red-600 text-base font-medium 
          shadow-sm transition-all duration-200">

    <i class="fas fa-arrow-left mr-2 text-lg"></i>
    <span class="font-semibold text-sm">Retour</span>
</a>



        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-red-600">
                <i class="fas fa-question-circle mr-2"></i> Aide & Support
            </h1>
            <p class="text-gray-500 mt-2">Trouvez rapidement une réponse ou contactez-nous</p>
        </div>

        <!-- FAQ rapide -->
        <div class="space-y-4">
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-truck mr-2 text-red-500"></i>
                    Comment créer une livraison ?
                </h3>
                <p class="text-gray-600 text-sm mt-2">
                    Depuis votre tableau de bord, cliquez sur 
                    <span class="font-semibold text-red-600">“Nouvelle Livraison”</span>, remplissez les informations 
                    (adresse, destinataire, etc.) puis validez.
                </p>
            </div>

            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                    Comment suivre ma commande ?
                </h3>
                <p class="text-gray-600 text-sm mt-2">
                    Rendez-vous dans <span class="font-semibold">“Livraison en cours”</span> sur la page d’accueil. 
                </p>
            </div>

            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-headset mr-2 text-blue-500"></i>
                    Comment contacter le support ?
                </h3>
                <p class="text-gray-600 text-sm mt-2">
                    Vous avez deux options pour nous contacter :
                </p>
                <ul class="list-disc list-inside text-gray-600 text-sm mt-2 space-y-1">
                    <li>
                        Envoyer un message directement à l’administrateur via 
                        <a href="{{ route('user.messages') }}" class="text-red-600 font-semibold hover:underline">
                            l’onglet “Voir mes messages”
                        </a> dans votre profil.
                    </li>
                    <li>
                        Ou nous écrire par email : 
                        <a href="mailto:support@colisfast.sn" class="text-blue-600 hover:underline">
                            support@colisfast.sn
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bouton de contact -->
        <div class="mt-6 text-center">
            <a href="mailto:support@colisfast.sn"
               class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow transition">
                <i class="fas fa-envelope mr-2"></i> Contacter le support
            </a>
        </div>

        <!-- Footer info -->
        <p class="text-xs text-gray-400 text-center mt-6">
            Nous sommes disponibles 7j/7 pour vous accompagner 
        </p>
    </div>
</div>
@endsection

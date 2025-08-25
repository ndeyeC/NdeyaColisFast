@php
    // Déterminer le layout selon le rôle
    $layout = Auth::user()->role === 'livreur' ? 'layouts.master' : 'layouts.template';
@endphp

@extends($layout)

@section('title', 'Modifier mon profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <!-- Titre principal -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 flex justify-center items-center gap-3">
            <i class="fas fa-user-cog text-blue-500"></i>
            Modifier mon profil
        </h1>
        <p class="text-gray-500 mt-2">Gérez vos informations personnelles, votre mot de passe et votre compte</p>
    </div>

    <!-- Carte profil -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 space-y-8 border border-gray-100">

        <!-- ✅ 1. Informations du profil -->
        <div>
            <h2 class="text-xl font-semibold flex items-center gap-2 mb-4">
                <i class="fas fa-id-card-alt text-green-500"></i> Informations personnelles
            </h2>

            <div class="bg-gray-50 p-5 rounded-xl border">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Séparateur -->
        <div class="relative flex items-center justify-center">
            <span class="absolute bg-white px-4 text-gray-400 text-sm">⚡ Sécurité</span>
            <div class="w-full h-px bg-gray-200"></div>
        </div>

        <!-- ✅ 2. Modifier le mot de passe -->
        <div>
            <h2 class="text-xl font-semibold flex items-center gap-2 mb-4">
                <i class="fas fa-lock text-yellow-500"></i> Changer mon mot de passe
            </h2>

            <div class="bg-gray-50 p-5 rounded-xl border">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Séparateur -->
        <div class="relative flex items-center justify-center">
            <span class="absolute bg-white px-4 text-gray-400 text-sm">🛑 Danger Zone</span>
            <div class="w-full h-px bg-gray-200"></div>
        </div>

        <!-- ✅ 3. Supprimer le compte -->
        <div>
            <h2 class="text-xl font-semibold flex items-center gap-2 mb-4 text-red-600">
                <i class="fas fa-user-times"></i> Supprimer mon compte
            </h2>

            <div class="bg-red-50 p-5 rounded-xl border border-red-200">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>

    <!-- ✅ Bouton retour -->
    <div class="text-center mt-10">
        @if(Auth::user()->role === 'livreur')
            <a href="{{ route('livreur.dashboarde') }}" 
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-gray-700 hover:bg-gray-900 text-white shadow transition">
                <i class="fas fa-arrow-left"></i> Retour au Dashboard Livreurs
            </a>
        @else
            <a href="{{ route('dashboard') }}" 
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white shadow transition">
                <i class="fas fa-arrow-left"></i> Retour au Dashboard Client
            </a>
        @endif
    </div>

</div>

<!-- ✅ Petites animations sur les cartes -->
<style>
    .bg-gray-50:hover {
        background: #f9fafb;
        transition: 0.3s ease-in-out;
    }
</style>
@endsection

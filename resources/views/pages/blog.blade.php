@extends('layouts.page')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Blog</h1>

    <p class="mb-6">Bienvenue sur notre blog ! Découvrez nos actualités, conseils et nouveautés concernant notre service de livraison.</p>

    <div class="space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold">📦 Comment optimiser vos livraisons</h2>
            <p class="text-gray-700">Découvrez nos astuces pour réduire les délais et améliorer l'expérience client...</p>
            <a href="#" class="text-red-500 hover:underline">Lire la suite</a>
        </div>

        <div class="border-b pb-4">
            <h2 class="text-xl font-semibold">🚀 Nouveaux services disponibles</h2>
            <p class="text-gray-700">Nous avons ajouté de nouvelles fonctionnalités pour rendre vos commandes encore plus simples...</p>
            <a href="#" class="text-red-500 hover:underline">Lire la suite</a>
        </div>
    </div>
</div>
@endsection

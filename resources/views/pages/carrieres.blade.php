@extends('layouts.page')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Rejoignez notre équipe</h1>

    <p>Nous sommes toujours à la recherche de livreurs motivés et de talents pour rejoindre notre équipe.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Postes disponibles</h2>
    <ul class="list-disc pl-5">
        <li>Livreur (moto, vélo, voiture)</li>
        <li>Service client</li>
        <li>Développeur web & mobile</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">Comment postuler</h2>
    <p>Envoyez votre CV et lettre de motivation à <strong>recrutement@votreapp.com</strong> ou remplissez notre formulaire en ligne.</p>

    <p class="mt-6 text-sm text-gray-500">Dernière mise à jour : {{ date('d/m/Y') }}</p>
</div>
@endsection

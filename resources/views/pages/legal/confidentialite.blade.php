@extends('layouts.page')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Politique de confidentialité</h1>

    <p>Cette politique de confidentialité explique comment notre application de livraison collecte, utilise et protège vos données personnelles.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">1. Données collectées</h2>
    <ul class="list-disc pl-5">
        <li>Nom et prénom</li>
        <li>Adresse e-mail et numéro de téléphone</li>
        <li>Adresse de livraison et de départ</li>
        <li>Informations de paiement (via nos prestataires sécurisés)</li>
        <li>Données de localisation (pour le suivi des livraisons)</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">2. Utilisation des données</h2>
    <p>Nous utilisons vos données pour :</p>
    <ul class="list-disc pl-5">
        <li>Traiter et livrer vos commandes</li>
        <li>Vous contacter en cas de problème avec une livraison</li>
        <li>Améliorer nos services et votre expérience utilisateur</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">3. Partage des données</h2>
    <p>Vos données peuvent être partagées avec :</p>
    <ul class="list-disc pl-5">
        <li>Nos livreurs</li>
        <li>Nos prestataires de paiement</li>
        <li>Nos partenaires logistiques</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">4. Durée de conservation</h2>
    <p>Nous conservons vos données pendant la durée nécessaire à la réalisation des services et pour respecter nos obligations légales.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">5. Vos droits</h2>
    <p>Conformément à la loi, vous disposez d’un droit d’accès, de rectification et de suppression de vos données personnelles. Vous pouvez exercer ces droits en nous contactant à : <strong>contact@votreapp.com</strong></p>

    <h2 class="text-xl font-semibold mt-6 mb-2">6. Sécurité</h2>
    <p>Nous mettons en œuvre des mesures de sécurité pour protéger vos données contre tout accès non autorisé.</p>

    <p class="mt-6 text-sm text-gray-500">Dernière mise à jour : {{ date('d/m/Y') }}</p>
</div>
@endsection

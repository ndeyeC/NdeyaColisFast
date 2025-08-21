@extends('layouts.page')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Mentions légales</h1>

    <h2 class="text-xl font-semibold mt-6 mb-2">1. Éditeur du site</h2>
    <p>Nom de l’entreprise : <strong>Ma Livraison Express</strong></p>
    <p>Forme juridique : SARL</p>
    <p>Adresse : 123 Rue Exemple, Dakar, Sénégal</p>
    <p>Téléphone : +221 77 123 45 67</p>
    <p>Email : contact@malivraison.com</p>
    <p>RCCM : SN-DKR-2025-A12345</p>
    <p>NINEA : 123456789</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">2. Hébergeur</h2>
    <p>Nom : OVH</p>
    <p>Adresse : 2 rue Kellermann, 59100 Roubaix, France</p>
    <p>Téléphone : +33 9 72 10 10 07</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">3. Propriété intellectuelle</h2>
    <p>Tout le contenu du site (textes, images, logos, code) est protégé par les lois sur la propriété intellectuelle. Toute reproduction est interdite sans autorisation écrite.</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">4. Responsabilité</h2>
    <p>Nous mettons tout en œuvre pour assurer l’exactitude des informations présentes sur le site, mais nous ne pouvons être tenus responsables des éventuelles erreurs ou interruptions de service.</p>

    <p class="mt-6 text-sm text-gray-500">Dernière mise à jour : {{ date('d/m/Y') }}</p>
</div>
@endsection

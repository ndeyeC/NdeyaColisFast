@extends('layouts.template')

@section('title', 'Créer une livraison')

@section('content')
<div class="bg-gray-50 min-h-screen pb-12">
    <!-- En-tête -->
    <div class="bg-indigo-600 py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-white">Créer une livraison</h1>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="#" class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-8">
        @csrf

        <!-- Détails du colis -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Détails du colis</h3>
            </div>
            <div class="px-6 py-5 space-y-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Ex : documents, vêtements..."></textarea>
                </div>
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700">Poids (kg)</label>
                    <input type="number" step="0.1" min="0.1" name="weight" id="weight" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="1.5">
                </div>
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700">Taille</label>
                    <select name="size" id="size" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="small">Petit</option>
                        <option value="medium">Moyen</option>
                        <option value="large">Grand</option>
                        <option value="xlarge">Très grand</option>
                    </select>
                </div>
                <div>
                    <label for="fragile" class="block text-sm font-medium text-gray-700">Fragilité</label>
                    <select name="fragile" id="fragile" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="0">Non fragile</option>
                        <option value="1">Fragile</option>
                        <option value="2">Très fragile</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Adresses -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Adresses</h3>
            </div>
            <div class="px-6 py-5 space-y-6">
                <div>
                    <label for="pickup_address" class="block text-sm font-medium text-gray-700">Adresse de ramassage</label>
                    <input type="text" name="pickup_address" id="pickup_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="123 Rue du Commerce, Paris">
                </div>
                <div>
                    <label for="delivery_address" class="block text-sm font-medium text-gray-700">Adresse de livraison</label>
                    <input type="text" name="delivery_address" id="delivery_address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="456 Avenue des Clients, Paris">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_name" class="block text-sm font-medium text-gray-700">Nom du destinataire</label>
                        <input type="text" name="contact_name" id="contact_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" name="contact_phone" id="contact_phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Options de livraison -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Options</h3>
            </div>
            <div class="px-6 py-5 space-y-6">
                <div>
                    <label for="delivery_type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="delivery_type" id="delivery_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="standard">Standard</option>
                        <option value="express">Express</option>
                    </select>
                </div>
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Créneau horaire</label>
                    <select name="scheduled_time" id="scheduled_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="morning">Matin (8h-12h)</option>
                        <option value="afternoon">Après-midi (12h-18h)</option>
                        <option value="evening">Soir (18h-22h)</option>
                    </select>
                </div>
                <div>
                    <label for="special_instructions" class="block text-sm font-medium text-gray-700">Instructions spéciales</label>
                    <textarea name="special_instructions" id="special_instructions" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Ex : digicode, étage..."></textarea>
                </div>
            </div>
        </div>

        <!-- Paiement -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Paiement</h3>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="flex items-center">
                    <input type="radio" name="payment_method" value="card" checked class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label class="ml-2 block text-sm text-gray-700">Carte bancaire</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" name="payment_method" value="cash" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label class="ml-2 block text-sm text-gray-700">Espèces</label>
                </div>
            </div>
        </div>

        <!-- Boutons -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Créer la livraison
            </button>
        </div>
    </form>
</div>
@endsection

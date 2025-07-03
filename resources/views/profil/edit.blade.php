@extends('layouts.template')

@section('content')
<div class="max-w-md mx-auto mt-8 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Modifier mon profil</h2>
    
    <form method="POST" action="{{ route('profil.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="name">Nom complet</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2" for="numero_telephone">Téléphone</label>
            <input type="tel" name="numero_telephone" id="numero_telephone" value="{{ old('numero_telephone', $user->numero_telephone) }}"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Ajoutez d'autres champs selon vos besoins -->

        <div class="mt-6 flex justify-between">
            <a href="{{ route('client.dashboard') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-red-400 text-white rounded-lg hover:bg-red-500 transition-colors">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
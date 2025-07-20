@extends('layouts.master')

@section('title', 'Déclarer un Trajet Urbain')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Déclarer un Trajet Urbain</h2>

    <form action="{{ route('livreur.trajets.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="type_voiture" class="block mb-2 font-semibold text-gray-700">Type de voiture</label>
            <input type="text" id="type_voiture" name="type_voiture" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition" />
            @error('type_voiture')
                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="matricule" class="block mb-2 font-semibold text-gray-700">Matricule</label>
            <input type="text" id="matricule" name="matricule" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition" />
            @error('matricule')
                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="heure_depart" class="block mb-2 font-semibold text-gray-700">Heure de départ</label>
            <input type="time" id="heure_depart" name="heure_depart" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition" />
            @error('heure_depart')
                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="destination_region" class="block mb-2 font-semibold text-gray-700">Destination (région)</label>
            <input type="text" id="destination_region" name="destination_region" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition" />
            @error('destination_region')
                <p class="text-red-500 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center space-x-2">
            <span>✅ Enregistrer</span>
        </button>
    </form>
</div>
@endsection

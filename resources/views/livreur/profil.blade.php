@extends('layouts.master')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Modifier mon profil</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium">Nom complet</label>
                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}"

                       class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', auth()->user()->email) }}">
    </div>

    <!-- Numéro téléphone -->
    <div class="mb-3">
        <label for="numero_telephone" class="form-label">Téléphone</label>
        <input type="text" name="numero_telephone" id="numero_telephone" class="form-control" value="{{ old('numero_telephone', auth()->user()->numero_telephone) }}">
    </div>

    <!-- Boutons -->
    <div class="flex justify-end space-x-2">
                <a href="{{ route('livreur.dashboarde') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-black font-semibold py-2 px-4 rounded">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection



@extends('layouts.template')

@section('title', 'Noter un livreur')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center text-red-600">Noter votre livreur</h1>

    <form action="{{ route('client.rate.livreur') }}" method="POST">
        @csrf

        <input type="hidden" name="commande_id" value="{{ $commande_id ?? '' }}">

        <div class="mb-4">
            <label class="block mb-2 font-semibold">Note</label>
            <div class="flex gap-2">
                @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                        <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-500 transition"
                             xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.785.57-1.84-.197-1.54-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/>
                        </svg>
                    </label>
                @endfor
            </div>
            @error('rating')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="commentaire" class="block mb-2 font-semibold">Votre avis (facultatif)</label>
            <textarea name="commentaire" id="commentaire" rows="4" class="w-full p-3 border rounded-lg resize-none" placeholder="Laissez un commentaire...">{{ old('commentaire') }}</textarea>
            @error('commentaire')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 transition">
            Envoyer la note
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-red-600">Annuler et revenir</a>
    </div>
</div>
@endsection

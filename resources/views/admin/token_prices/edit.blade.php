@extends('layouts.template')

@section('content')
<div class="p-6 max-w-md mx-auto">
    <h2 class="text-xl font-bold mb-4">Modifier le prix des jetons - {{ $zone->name }}</h2>

    <form method="POST" action="{{ route('admin.token-prices.update', $zone->id) }}">
        @csrf
        @method('PUT')

        <label class="block mb-2 font-medium">Prix du jeton (FCFA)</label>
        <input type="number" name="base_token_price" value="{{ old('base_token_price', $zone->base_token_price) }}"
               class="w-full border rounded px-4 py-2 @error('base_token_price') border-red-500 @enderror">
        @error('base_token_price')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <button type="submit"
                class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Enregistrer
        </button>
    </form>
</div>
@endsection

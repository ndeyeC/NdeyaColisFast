@extends('layouts.template')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Lien de retour -->
    <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm text-blue-600 hover:underline mb-4">
        <i class="fas fa-arrow-left mr-2"></i>
        Retour
    </a>

    <!-- Titre -->
    <h2 class="text-2xl font-bold mb-4">Gestion des prix des jetons</h2>

    <!-- Message de succès -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tableau des prix -->
    <table class="table-auto w-full bg-white rounded shadow overflow-hidden">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2">Zone</th>
                <th class="px-4 py-2">Prix du jeton (FCFA)</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($zones as $zone)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $zone->name }}</td>
                <td class="px-4 py-2">{{ number_format($zone->base_token_price, 0, ',', ' ') }}
                </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.token-prices.edit', $zone->id) }}" class="text-blue-500 hover:underline">
                            Modifier
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

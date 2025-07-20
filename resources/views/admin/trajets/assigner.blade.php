@extends('layouts.admin')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
    <h2 class="text-2xl font-extrabold mb-6 flex items-center gap-2 text-gray-800">
        🚚 Assigner des livraisons à 
        <span class="text-blue-600">{{ $trajet->livreur->name }}</span>
    </h2>

    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg shadow-sm mb-6">
        <p class="text-gray-700">
            <strong class="text-gray-900">📍 Destination :</strong> {{ $trajet->destination_region }}<br>
            <strong class="text-gray-900">🕒 Heure départ :</strong> {{ $trajet->heure_depart }}<br>
            <strong class="text-gray-900">🚗 Voiture :</strong> {{ $trajet->type_voiture }} <span class="text-gray-500">({{ $trajet->matricule }})</span>
        </p>
    </div>

    @if($livraisons->isEmpty())
        <div class="p-5 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-xl text-center font-medium shadow-sm">
            ❌ Aucune livraison disponible pour cette destination.
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('admin.trajets.urbains') }}"
               class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-xl shadow-md transition">
               ⬅ Retour aux trajets
            </a>
        </div>
    @else
        <form action="{{ route('admin.trajets.assigner', $trajet->id) }}" method="POST">
            @csrf

            <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 uppercase text-sm">
                            <th class="p-3 text-center">#</th>
                            <th class="p-3">👤 Client</th>
                            <th class="p-3">📦 Adresse départ</th>
                            <th class="p-3">🎯 Adresse arrivée</th>
                            <th class="p-3 text-right">💰 Prix</th>
                            <th class="p-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livraisons as $livraison)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 text-center">
                                <input type="checkbox" name="livraisons[]" value="{{ $livraison->id }}" class="w-5 h-5 rounded border-gray-300">
                            </td>
                            <td class="p-3 font-medium text-gray-800">
                                {{ $livraison->client->name ?? 'Client inconnu' }}
                            </td>
                            <td class="p-3 text-gray-600">{{ $livraison->adresse_depart }}</td>
                            <td class="p-3 text-gray-600">{{ $livraison->adresse_arrivee }}</td>
                            <td class="p-3 text-right font-semibold text-green-600">
                                {{ number_format($livraison->prix_final, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if($livraison->status == 'payee') bg-green-100 text-green-700 
                                    @elseif($livraison->status == 'en_attente') bg-yellow-100 text-yellow-700 
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($livraison->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('admin.trajets.urbains') }}"
                   class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-xl shadow-md transition">
                   ⬅ Annuler
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-2.5 rounded-xl shadow-md transition">
                    ✅ Assigner les livraisons sélectionnées
                </button>
            </div>
        </form>
    @endif
</div>
@endsection

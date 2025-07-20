@extends('layouts.master')

@section('title', 'Mes Trajets Urbains')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-lg mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Mes Trajets Urbains</h2>
    
    <div class="mb-6 flex justify-end">
        <a href="{{ route('livreur.trajets.create') }}" 
           class="px-5 py-3 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition font-semibold">
            + Déclarer un trajet
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left text-gray-700 font-semibold">
                    <th class="p-3 border">Type voiture</th>
                    <th class="p-3 border">Matricule</th>
                    <th class="p-3 border">Départ</th>
                    <th class="p-3 border">Destination</th>
                    <th class="p-3 border">Déclaré le</th>
                    <th class="p-3 border text-center">Actions</th> <!-- nouvelle colonne -->
                </tr>
            </thead>
            <tbody>
                @forelse($trajets as $trajet)
                <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition">
                    <td class="p-3 border">{{ $trajet->type_voiture }}</td>
                    <td class="p-3 border">{{ $trajet->matricule }}</td>
                    <td class="p-3 border">{{ \Carbon\Carbon::parse($trajet->heure_depart)->format('H:i') }}</td>
                    <td class="p-3 border">{{ $trajet->destination_region }}</td>
                    <td class="p-3 border">{{ $trajet->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-3 border text-center">
                        <a href="{{ route('livreur.trajets.commandes', $trajet->id) }}"
                           class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            📋 Voir les commandes
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500 italic">Aucun trajet déclaré pour le moment</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md">
    <h2 class="text-xl font-bold mb-4">Trajets déclarés par les livreurs urbains</h2>

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Livreur</th>
                <th class="p-2 border">Voiture</th>
                <th class="p-2 border">Matricule</th>
                <th class="p-2 border">Départ</th>
                <th class="p-2 border">Destination</th>
                <th class="p-2 border">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trajets as $trajet)
            <tr>
                <td class="p-2 border">{{ $trajet->livreur->name }}</td>
                <td class="p-2 border">{{ $trajet->type_voiture }}</td>
                <td class="p-2 border">{{ $trajet->matricule }}</td>
                <td class="p-2 border">{{ $trajet->heure_depart }}</td>
                <td class="p-2 border">{{ $trajet->destination_region }}</td>
                <td class="p-2 border">{{ $trajet->created_at->format('d/m/Y') }}</td>
                <td class="p-2 border text-center">
                    <!-- Bouton qui envoie vers la liste des livraisons pour cette destination -->
                    <form method="GET" action="{{ route('admin.trajets.assigner', $trajet->id) }}">
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

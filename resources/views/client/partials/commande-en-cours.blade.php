
<div class="bg-white shadow p-5 rounded-xl border mb-4">
    <div class="flex justify-between items-start">
        <div>
            <h4 class="font-bold text-gray-800">Commande #{{ $commande->id }}</h4>
            <p class="text-gray-600 mt-1">
                {{ Str::limit($commande->adresse_depart, 25) }} → {{ Str::limit($commande->adresse_arrivee, 25) }}
            </p>
            @if($commande->driver)
                <p class="text-sm text-gray-600 mt-1">
                    Livreur : {{ $commande->driver->name }}
                </p>
            @else
                <p class="text-sm text-gray-600 mt-1">
                    Livreur : Non assigné
                </p>
            @endif
        </div>
        <div class="text-right">
            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                {{ number_format($commande->prix_final, 0, '', ' ') }} FCFA
            </span>
        </div>
    </div>
</div>

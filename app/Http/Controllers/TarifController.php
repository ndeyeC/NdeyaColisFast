<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarif;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::orderBy('zone')
                     ->orderBy('type_livraison')
                     ->paginate(20);

        return view('admin.tarifs.index', compact('tarifs'));
    }

    public function create()
    {
        return view('admin.tarifs.create', [
            'distances' => Tarif::distancesOptions(),
            'poids' => Tarif::poidsOptions(),
            'typeZones' => Tarif::typeZoneOptions(), 
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone' => 'required|string|max:100',
            'type_zone' => 'required|in:intra_urbaine,region_proche,region_eloignee,extra_urbaine', // ← Nouveau
            'type_livraison' => 'required|string|max:50',
            'tranche_distance' => 'required|string',
            'tranche_poids' => 'required|string',
            'prix' => 'required|numeric|min:0'
        ]);

        Tarif::create($validated);

        return redirect()->route('admin.tarifs.index')
                         ->with('success', 'Tarif créé avec succès');
    }

    public function edit(Tarif $tarif)
    {
        return view('admin.tarifs.edit', [
            'tarif' => $tarif,
            'distances' => Tarif::distancesOptions(),
            'poids' => Tarif::poidsOptions(),
           'typeZones' => Tarif::typeZoneOptions(), 
        ]);
    }

    public function update(Request $request, Tarif $tarif)
    {
        $validated = $request->validate([
            'zone' => 'required|string|max:100',
            'type_zone' => 'required|in:intra_urbaine,region_proche,region_eloignee,extra_urbaine',
            'type_livraison' => 'required|string|max:50',
            'tranche_distance' => 'required|string',
            'tranche_poids' => 'required|string',
            'prix' => 'required|numeric|min:0'
        ]);

        $tarif->update($validated);

        return redirect()->route('admin.tarifs.index')
                         ->with('success', 'Tarif mis à jour avec succès');
    }

    public function destroy(Tarif $tarif)
    {
        $tarif->delete();

        return redirect()->route('admin.tarifs.index')
                         ->with('success', 'Tarif supprimé avec succès');
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Zone;
use App\Models\Tarif;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'adresse_depart' => 'required|string|max:255',
            'adresse_arrivee' => 'required|string|max:255',
            'type_livraison' => 'required|in:standard,express',
            'type_colis' => 'required|string' // exemple : 'Petit (<5kg)', 'Moyen (5-15kg)', etc.
        ]);

        // Extraire les villes ou régions depuis les adresses (simple version sans API)
        $regionDepart = $this->extraireRegionDepuisAdresse($request->adresse_depart);
        $regionArrivee = $this->extraireRegionDepuisAdresse($request->adresse_arrivee);

        // Rechercher la zone
        $zone = Zone::where('region_depart', $regionDepart)
                    ->where('region_arrivee', $regionArrivee)
                    ->first();

        if (!$zone) {
            return back()->with('error', 'Aucune zone correspondante trouvée pour ces adresses.');
        }

        // Déterminer la tranche de poids à partir du type de colis
        $tranchePoids = $this->getTranchePoids($request->type_colis);

        // Rechercher le tarif correspondant
        $tarif = Tarif::where('type_zone', $zone->type_zone)
                      ->where('type_livraison', $request->type_livraison)
                      ->where('tranche_poids', $tranchePoids)
                      ->first();

        if (!$tarif) {
            return back()->with('error', 'Aucun tarif défini pour cette combinaison.');
        }

        // Créer la commande
        Commande::create([
            'adresse_depart' => $request->adresse_depart,
            'adresse_arrivee' => $request->adresse_arrivee,
            'type_livraison' => $request->type_livraison,
            'prix' => $tarif->prix,
            'quantite' => 1, // ou autre logique
            'user_id' => Auth::id()
        ]);

        return redirect()->route('commandes.index')->with('success', 'Commande enregistrée avec succès.');
    }

    private function extraireRegionDepuisAdresse($adresse)
    {
        // Cette logique est simplifiée : dans la vraie vie, mieux avec API ou analyse plus robuste
        $regionsConnues = ['Dakar', 'Thiès', 'Kaolack', 'Mbour', 'Louga', 'Ziguinchor', 'Saint-Louis', 'Rufisque', 'Pikine', 'Guédiawaye', 'Matam', 'Tambacounda', 'Kolda', 'Fatick'];

        foreach ($regionsConnues as $region) {
            if (stripos($adresse, $region) !== false) {
                return $region;
            }
        }

        return null;
    }

    private function getTranchePoids($typeColis)
    {
        // Correspondance simple selon type de colis
        return match ($typeColis) {
            'Petit (<5kg)' => '0-5 kg',
            'Moyen (5-15kg)' => '5-20 kg',
            'Gros (>15kg)' => '20-50 kg',
            default => '50+ kg',
        };
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Commnande;
use App\Models\TrajetUrbain;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard principal de l'admin (statistiques)
     */
    public function index()
    {
        $totalLivreurs = User::where('role', 'livreur')->count();

        $livreursEnAttente = Commnande::where('status', 'en_attente')
            ->distinct('driver_id')
            ->count('driver_id');

        $totalLivraisons = Commnande::whereNotNull('driver_id')->count();

        $revenusTotaux = Commnande::whereIn('status', [
            Commnande::STATUT_PAYEE ?? 'payee',
            Commnande::STATUT_CONFIRMEE ?? 'confirmee',
            Commnande::STATUT_LIVREE ?? 'livree',
        ])->sum('prix_final');

        // Livraisons par mois pour l'année en cours
        $livraisons = Commnande::whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->groupBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        // Revenus mensuels pour l'année en cours
        $revenusMensuels = Commnande::whereYear('created_at', now()->year)
            ->whereIn('status', ['payee', 'livree'])
            ->selectRaw('MONTH(created_at) as mois, SUM(prix_final) as total')
            ->groupBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        // Répartition des statuts
        $statuts = Commnande::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statutsLabels = array_map(fn($s) => ucfirst(str_replace('_', ' ', $s)), array_keys($statuts));

        // Labels & valeurs des graphiques
        $moisLabels = [];
        $livraisonsParMois = [];
        $revenusParMois = [];

        for ($m = 1; $m <= 12; $m++) {
            $moisLabels[] = ucfirst(Carbon::create()->month($m)->locale('fr')->monthName);
            $livraisonsParMois[] = $livraisons[$m] ?? 0;
            $revenusParMois[] = $revenusMensuels[$m] ?? 0;
        }

        // Top 5 livreurs par nombre de livraisons terminées
        $topLivreurs = User::where('role', 'livreur')
            ->withCount(['livraisons' => fn($q) => $q->where('status', 'livree')])
            ->orderByDesc('livraisons_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalLivreurs',
            'livreursEnAttente',
            'totalLivraisons',
            'revenusTotaux',
            'moisLabels',
            'livraisonsParMois',
            'revenusParMois',
            'statuts',
            'topLivreurs',
            'statutsLabels'
        ));
    }

    /**
     * Liste des trajets déclarés par les livreurs urbains
     */
    public function trajetsUrbains()
    {
        $trajets = TrajetUrbain::with('livreur')->latest()->get();
        return view('admin.trajets.index', compact('trajets'));
    }

    /**
     * Affiche les livraisons disponibles pour une destination de trajet
     */
    public function showLivraisonsPourAssignation($trajetId)
    {
        $trajet = TrajetUrbain::with('livreur')->findOrFail($trajetId);

    $livraisons = Commnande::whereNull('driver_id')   
        ->where('status', '!=', 'livree')             
        ->where(function($query) use ($trajet) {
            // ✅ Filtrer par région de destination du trajet
            $query->where('region_arrivee', $trajet->destination_region)
                  ->orWhere('region_depart', $trajet->destination_region);
        })
        ->get();


        return view('admin.trajets.assigner', compact('trajet', 'livraisons'));
    }



public function assignerLivraisons(Request $request, $trajetId)
{
    $trajet = TrajetUrbain::with('livreur')->findOrFail($trajetId);

    // Vérifier que le livreur est de type urbain
    if ($trajet->livreur->type_livreur !== 'urbain') {
        return redirect()->route('admin.trajets.urbains')
            ->with('error', 'Erreur : Le livreur n\'est pas de type urbain.');
    }

    $livraisonsIds = $request->input('livraisons', []);

    if (!empty($livraisonsIds)) {
        Commnande::whereIn('id', $livraisonsIds)->update([
            'driver_id' => $trajet->livreur->id,
            'trajet_id' => $trajet->id,
            'status' => 'payee'
        ]);

        // Envoyer une notification push au livreur
        $this->sendPushNotification(
            $trajet->livreur->fcm_token,
            "Nouvelles livraisons assignées",
            "Vous avez reçu " . count($livraisonsIds) . " nouvelle(s) livraison(s) pour votre trajet vers " . $trajet->destination_region
        );
    }

    return redirect()->route('admin.trajets.urbains')
        ->with('success', '✅ Les livraisons ont été assignées à ' . $trajet->livreur->name);
}
}

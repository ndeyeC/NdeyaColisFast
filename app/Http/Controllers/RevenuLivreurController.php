<?php

namespace App\Http\Controllers;

use App\Models\Commnande;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RevenuLivreurController extends Controller
{
    public function getStats($livreurId)
    {
        $revenuMois = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->sum('revenu_livreur');

        $revenuTotal = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->sum('revenu_livreur');

        $livraisonsMois = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->count();

        $moyenne = $livraisonsMois > 0 ? $revenuMois / $livraisonsMois : 0;

        return response()->json([
            'revenu_mois' => round($revenuMois, 2),
            'revenu_total' => round($revenuTotal, 2),
            'livraisons_mois' => $livraisonsMois,
            'moyenne_livraison' => round($moyenne, 2)
        ]);
    }

    
    public function getHistoriquePaiements($livreurId)
    {
        $paiements = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereNotNull('revenu_livreur')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($commande) {
                return [
                    'reference' => 'CMD-' . $commande->id,
                    'date' => $commande->updated_at->format('d/m/Y'),
                    'description' => 'Livraison #' . $commande->id,
                    'montant' => $commande->revenu_livreur,
                    'statut' => $commande->statut_paiement_livreur ?? 'en_attente'
                ];
            });

        return response()->json($paiements);
    }

    public function getGraphData($livreurId)
    {
        $revenusJournaliers = [];
        $dates = [];
        
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates[] = $date->format('d M');
            
            $total = Commnande::where('driver_id', $livreurId)
                ->where('status', Commnande::STATUT_LIVREE)
                ->whereDate('updated_at', $date)
                ->sum('revenu_livreur');
                
            $revenusJournaliers[] = $total;
        }

        $typesLivraison = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->selectRaw('type_livraison, sum(revenu_livreur) as total')
            ->groupBy('type_livraison')
            ->get()
            ->pluck('total', 'type_livraison');

        return response()->json([
            'revenus_journaliers' => $revenusJournaliers,
            'dates' => $dates,
            'repartition_types' => $typesLivraison
        ]);
    }

    public function getPerformances($livreurId)
    {
        // Calcul des bonus
        $livraisonsMois = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->count();

        $noteMoyenne = Commnande::where('driver_id', $livreurId)
            ->whereNotNull('note_client')
            ->avg('note_client');

        $livraisonsExpress = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->where('type_livraison', 'express')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->count();

        return response()->json([
            'objectifs' => [
                [
                    'nom' => '50 livraisons',
                    'progression' => $livraisonsMois,
                    'total' => 50,
                    'recompense' => 5000
                ],
                [
                    'nom' => 'Note 4.8+',
                    'progression' => round($noteMoyenne, 1),
                    'total' => 5.0,
                    'recompense' => 3000
                ],
                [
                    'nom' => '20 livraisons express',
                    'progression' => $livraisonsExpress,
                    'total' => 20,
                    'recompense' => 4000
                ]
            ],
            'solde_portefeuille' => Commnande::where('driver_id', $livreurId)
                ->where('statut_paiement_livreur', 'en_attente')
                ->sum('revenu_livreur')
        ]);
    }
}
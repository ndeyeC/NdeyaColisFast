<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Commnande;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatistiquesController extends Controller
{
    public function index()
    {
        $livreurId = auth()->id(); // Filtrer par livreur connecté

        $statistiques = $this->getStatistiques($livreurId);
        $evaluations = $this->getDernieresEvaluations($livreurId);
        $performance = $this->getPerformance($livreurId);

        return view('statistiques.index', compact(
            'statistiques',
            'evaluations',
            'performance'
        ));
    }

    private function getStatistiques($livreurId)
    {
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        $livraisonsMois = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->count();

        $revenusMois = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->sum('prix_final');

        $totalCommandes = Commnande::where('driver_id', $livreurId)
            ->whereBetween('created_at', [$debutMois, $finMois])
            ->count();

        $commandesReussies = $livraisonsMois;

        $tauxReussite = $totalCommandes > 0 ? round(($commandesReussies / $totalCommandes) * 100, 1) : 0;

        $evaluationMoyenne = Evaluation::whereHas('commnande', function ($query) use ($livreurId, $debutMois, $finMois) {
            $query->where('driver_id', $livreurId)
                  ->whereBetween('updated_at', [$debutMois, $finMois]);
        })->avg('note') ?? 0;

        return [
            'livraisons_mois' => $livraisonsMois,
            'revenus_mois' => round($revenusMois, 2),
            'taux_reussite' => $tauxReussite,
            'evaluation_moyenne' => round($evaluationMoyenne, 1)
        ];
    }

    private function getDernieresEvaluations($livreurId)
    {
        return Evaluation::whereHas('commnande', function ($query) use ($livreurId) {
                $query->where('driver_id', $livreurId);
            })
            ->with(['commnande.user'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($evaluation) {
                return [
                    'client' => $evaluation->commnande->user->name ?? 'Client anonyme',
                    'date' => $evaluation->created_at->format('d M Y'),
                    'note' => $evaluation->note,
                    'commentaire' => $evaluation->commentaire ?? 'Aucun commentaire'
                ];
            });
    }

    private function getPerformance($livreurId)
    {
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        $tempsLivraisonMoyen = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->whereNotNull('temps_livraison')
            ->avg('temps_livraison') ?? 0;

        $totalLivraisons = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->count();

        $livraisonsCompletes = Commnande::where('driver_id', $livreurId)
            ->where('status', Commnande::STATUT_LIVREE)
            ->where('livraison_complete', true)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->count();

        $pourcentageLivraisonsCompletes = $totalLivraisons > 0 ? 
            round(($livraisonsCompletes / $totalLivraisons) * 100) : 0;

        $totalEvaluations = Evaluation::whereHas('commnande', function($query) use ($livreurId, $debutMois, $finMois) {
            $query->where('driver_id', $livreurId)
                  ->whereBetween('updated_at', [$debutMois, $finMois]);
        })->count();

        $evaluationsPositives = Evaluation::where('note', '>=', 4)
            ->whereHas('commnande', function($query) use ($livreurId, $debutMois, $finMois) {
                $query->where('driver_id', $livreurId)
                      ->whereBetween('updated_at', [$debutMois, $finMois]);
            })->count();

        $pourcentageEvaluationsPositives = $totalEvaluations > 0 ? 
            round(($evaluationsPositives / $totalEvaluations) * 100) : 0;

        return [
            'temps_livraison_moyen' => round($tempsLivraisonMoyen),
            'pourcentage_livraisons_completes' => $pourcentageLivraisonsCompletes,
            'pourcentage_evaluations_positives' => $pourcentageEvaluationsPositives
        ];
    }
}

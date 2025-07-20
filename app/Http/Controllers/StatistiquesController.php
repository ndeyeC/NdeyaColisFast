<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Commnande;

class StatistiquesController extends Controller
{
    public function index()
    {
        $statistiques = $this->getStatistiques();
        $graphiques = $this->getDonneesGraphiques();
        $evaluations = $this->getDernieresEvaluations();
        $performance = $this->getPerformance();

        return view('statistiques.index', compact(
            'statistiques',
            'graphiques',
            'evaluations',
            'performance'
        ));
    }

    private function getStatistiques()
    {
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        $livraisonsMois = Commnande::where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->count();

        $revenusMois = Commnande::where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->sum('prix_final');

        $totalCommandes = Commnande::whereBetween('created_at', [$debutMois, $finMois])->count();
        $commandesReussies = Commnande::where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->count();
        
        $tauxReussite = $totalCommandes > 0 ? round(($commandesReussies / $totalCommandes) * 100, 1) : 0;

        $evaluationMoyenne = Evaluation::whereHas('commnande', function($query) use ($debutMois, $finMois) {
            $query->whereBetween('updated_at', [$debutMois, $finMois]);
        })->avg('note') ?? 0;

        return [
            'livraisons_mois' => $livraisonsMois,
            'revenus_mois' => $revenusMois,
            'taux_reussite' => $tauxReussite,
            'evaluation_moyenne' => round($evaluationMoyenne, 1)
        ];
    }

    private function getDonneesGraphiques()
    {
        $evolutionLivraisons = [];
        $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Commnande::where('status', Commnande::STATUT_LIVREE)
                ->whereDate('updated_at', $date)
                ->count();
            $evolutionLivraisons[] = $count;
        }

        return [
            'evolution_livraisons' => $evolutionLivraisons,
            'jours' => $jours
        ];
    }

    private function getDernieresEvaluations()
    {
        return Evaluation::with(['commnande.user'])
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

    private function getPerformance()
    {
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        $tempsLivraisonMoyen = Commnande::where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->whereNotNull('temps_livraison')
            ->avg('temps_livraison') ?? 25;

        $totalLivraisons = Commnande::where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->count();
        
        $livraisonsCompletes = Commnande::where('status', Commnande::STATUT_LIVREE)
            ->where('livraison_complete', true)
            ->whereBetween('updated_at', [$debutMois, $finMois])
            ->count();

        $pourcentageLivraisonsCompletes = $totalLivraisons > 0 ? 
            round(($livraisonsCompletes / $totalLivraisons) * 100) : 0;

        $totalEvaluations = Evaluation::whereHas('commnande', function($query) use ($debutMois, $finMois) {
            $query->whereBetween('updated_at', [$debutMois, $finMois]);
        })->count();

        $evaluationsPositives = Evaluation::where('note', '>=', 4)
            ->whereHas('commnande', function($query) use ($debutMois, $finMois) {
                $query->whereBetween('updated_at', [$debutMois, $finMois]);
            })->count();

        $pourcentageEvaluationsPositives = $totalEvaluations > 0 ? 
            round(($evaluationsPositives / $totalEvaluations) * 100) : 0;

        return [
            'temps_livraison_moyen' => round($tempsLivraisonMoyen),
            'pourcentage_livraisons_completes' => $pourcentageLivraisonsCompletes,
            'pourcentage_evaluations_positives' => $pourcentageEvaluationsPositives
        ];
    }

    public function getStatistiquesPeriode(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        
        switch ($periode) {
            case '3mois':
                $debut = Carbon::now()->subMonths(3);
                break;
            case 'annee':
                $debut = Carbon::now()->startOfYear();
                break;
            default:
                $debut = Carbon::now()->startOfMonth();
        }

        $fin = Carbon::now();

        $livraisons = Commnande::where('status', Commnande::STATUT_LIVREE)
            ->whereBetween('updated_at', [$debut, $fin])
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($livraisons);
    }
}
<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Commnande;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLivreurs = User::where('role', 'livreur')->count();

        // $livreursEnAttente = User::where('role', 'livreur')
        //     ->where('status', 'en_attente') // adapte selon ton modèle
        //     ->count();
        $livreursEnAttente = Commnande::where('status', 'en_attente')->distinct('driver_id')->count('driver_id');


        $totalLivraisons = Commnande::whereNotNull('driver_id')->count();

        $revenusTotaux = Commnande::whereIn('status', [
            Commnande::STATUT_PAYEE,
            Commnande::STATUT_CONFIRMEE,
            Commnande::STATUT_LIVREE,
        ])->sum('prix_final');

        $livraisons = Commnande::whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->groupBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        $revenusMensuels = Commnande::whereYear('created_at', now()->year)
            ->whereIn('status', [Commnande::STATUT_PAYEE, Commnande::STATUT_LIVREE])
            ->selectRaw('MONTH(created_at) as mois, SUM(prix_final) as total')
            ->groupBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        $statuts = Commnande::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
            $statutsLabels = array_map(function ($s) {
              return ucfirst(str_replace('_', ' ', $s));
              }, array_keys($statuts));


        $moisLabels = [];
        $livraisonsParMois = [];
        $revenusParMois = [];

        for ($m = 1; $m <= 12; $m++) {
            $moisLabels[] = ucfirst(\Carbon\Carbon::create()->month($m)->locale('fr')->monthName);
            $livraisonsParMois[] = $livraisons[$m] ?? 0;
            $revenusParMois[] = $revenusMensuels[$m] ?? 0;
        }

        $topLivreurs = User::where('role', 'livreur')
            ->withCount(['livraisons' => fn ($q) => $q->where('status', Commnande::STATUT_LIVREE)])
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
}

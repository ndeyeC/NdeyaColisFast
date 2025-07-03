<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commnande;
use App\Models\User;

class ClientController extends Controller
{
    /**
     * Afficher le tableau de bord client
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Récupérer la commande en cours du client (acceptée ou en cours)
        $commandeEnCours = Commnande::where('user_id', $user->id)
            ->whereIn('status', ['acceptee', 'en_cours'])
            ->with(['driver'])
            ->latest()
            ->first();
        
        // Récupérer les livreurs disponibles (ceux qui n'ont pas de livraison en cours)
        $livreursDisponibles = User::where('role', 'livreur')
            ->where('status', 'actif')
            ->whereDoesntHave('commandesEnCours', function($query) {
                $query->where('status', 'en_cours');
            })
            ->withCount(['commandesLivrees' => function($query) {
                $query->where('status', 'livree');
            }])
            ->withAvg('evaluations', 'note')
            ->limit(6)
            ->get();
        
        // Statistiques du client
        $statistiques = [
            'total_commandes' => Commnande::where('user_id', $user->id)->count(),
            'commandes_livrees' => Commnande::where('user_id', $user->id)->where('status', 'livree')->count(),
            'note_moyenne' => $user->evaluations()->avg('note') ?? 0,
            'montant_total' => Commnande::where('user_id', $user->id)->where('status', 'livree')->sum('prix_final')
        ];
        
        return view('client.dashboard', compact('commandeEnCours', 'livreursDisponibles', 'statistiques'));
    }
    
    /**
     * API pour récupérer les données en temps réel
     */
    public function apiDashboardData()
    {
        $user = Auth::user();
        
        $commandeEnCours = Commnande::where('user_id', $user->id)
            ->whereIn('status', ['acceptee', 'en_cours'])
            ->with(['driver'])
            ->latest()
            ->first();
        
        $livreursDisponibles = User::where('role', 'livreur')
            ->where('status', 'actif')
            ->whereDoesntHave('commandesEnCours', function($query) {
                $query->where('status', 'en_cours');
            })
            ->withCount(['commandesLivrees' => function($query) {
                $query->where('status', 'livree');
            }])
            ->withAvg('evaluations', 'note')
            ->limit(6)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'commande_en_cours' => $commandeEnCours ? $this->formatCommandeData($commandeEnCours) : null,
                'livreurs_disponibles' => $livreursDisponibles->map(function($livreur) {
                    return $this->formatLivreurData($livreur);
                })
            ]
        ]);
    }
    
    /**
     * Formater les données de commande
     */
    private function formatCommandeData($commande)
    {
        return [
            'id' => $commande->id,
            'reference' => $commande->reference,
            'status' => $commande->status,
            'status_label' => $this->getStatusLabel($commande->status),
            'adresse_depart' => $commande->adresse_depart,
            'adresse_arrivee' => $commande->adresse_arrivee,
            'prix_final' => $commande->prix_final,
            'type_livraison' => $commande->type_livraison,
            'driver' => $commande->driver ? [
                'id' => $commande->driver->id,
                'name' => $commande->driver->name,
                'photo' => $commande->driver->photo ?? 'https://i.pravatar.cc/40?img=' . rand(1, 10),
                'telephone' => $commande->driver->numero_telephone,
                'note_moyenne' => $commande->driver->evaluations()->avg('note') ?? 0
            ] : null,
            'created_at' => $commande->created_at->format('H:i'),
            'updated_at' => $commande->updated_at->format('H:i')
        ];
    }
    
    /**
     * Formater les données de livreur
     */
    private function formatLivreurData($livreur)
    {
        return [
            'id' => $livreur->id,
            'name' => $livreur->name,
            'photo' => $livreur->photo ?? 'https://i.pravatar.cc/60?img=' . rand(1, 10),
            'note_moyenne' => round($livreur->evaluations_avg_note ?? 0, 1),
            'total_livraisons' => $livreur->commandes_livrees_count ?? 0,
            'telephone' => $livreur->numero_telephone,
            'vehicule' => $livreur->vehicule ?? 'Moto',
            'zone_coverage' => $livreur->zone_coverage ?? 'Dakar'
        ];
    }
    
    /**
     * Obtenir le libellé du statut
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'en_attente_paiement' => 'En attente de paiement',
            'payee' => 'Payée',
            'acceptee' => 'Acceptée',
            'en_cours' => 'En cours',
            'livree' => 'Livrée',
            'annulee' => 'Annulée'
        ];
        
        return $labels[$status] ?? $status;
    }
    
    /**
     * Ajouter un livreur aux favoris
     */
    public function ajouterLivreurFavori(Request $request)
    {
        $request->validate([
            'livreur_id' => 'required|exists:users,id'
        ]);
        
        $user = Auth::user();
        
        // Ajouter à la table des favoris (à créer si nécessaire)
        $user->livreursFavoris()->syncWithoutDetaching([$request->livreur_id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Livreur ajouté aux favoris'
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commnande;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Validator;

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
        // Livraisons terminées à noter (pas encore évaluées par ce client)
    $livraisonsTerminees = Commnande::where('user_id', $user->id)
        ->where('status', 'livree')
        ->whereDoesntHave('evaluations', function($q) {
            $q->where('type_evaluation', 'client');
        })
        ->with('driver')
        ->latest()
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

public function noterLivreur(Request $request)
{
    $validator = Validator::make($request->all(), [
        'commande_id' => 'required|exists:commnandes,id',
        'rating' => 'required|integer|min:1|max:5',
        'commentaire' => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $commande = Commnande::with('driver')->findOrFail($request->commande_id);

    // ✅ Vérifier que la commande appartient bien au client connecté
    if ($commande->user_id !== auth()->id()) {
        abort(403, 'Vous n’êtes pas autorisé à noter cette livraison.');
    }

    // ✅ Vérifier qu’un livreur est bien associé
    if (!$commande->driver) {
        return redirect()->back()->with('error', 'Cette livraison n’a pas de livreur assigné.');
    }

    // ✅ Vérifier si le client a déjà évalué ce livreur pour cette commande
    $evaluationExistante = Evaluation::where('commande_id', $commande->id)
        ->where('user_id', auth()->id())  // le client
        ->where('driver_id', $commande->driver->id)
        ->where('type_evaluation', 'client')
        ->first();

    if ($evaluationExistante) {
        return redirect()->back()->with('error', 'Vous avez déjà évalué ce livreur pour cette livraison.');
    }

    // ✅ Créer la nouvelle évaluation
    Evaluation::create([
        'commande_id' => $commande->id,
        'user_id' => auth()->id(),                // le client qui évalue
        'driver_id' => $commande->driver->id,     // le livreur évalué
        'note' => $request->rating,
        'commentaire' => $request->commentaire,
        'type_evaluation' => 'client',            // évaluation côté client
    ]);

    return redirect()->route('client.dashboard')->with('success', '✅ Merci pour votre évaluation !');
}


}
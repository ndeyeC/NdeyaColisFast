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
    
    $commandeEnCours = Commnande::where('user_id', $user->user_id) 
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
        ->with('evaluationsClients') 
        ->latest()
        ->limit(4) 
        ->get();

    $livraisonsTerminees = Commnande::where('user_id', $user->user_id) 
        ->where('status', 'livree')
        ->whereDoesntHave('evaluations', function($q) {
            $q->where('type_evaluation', 'client');
        })
        ->with('driver')
        ->latest()
        ->limit(2) 
        ->get();
        
    $statistiques = [
        'total_commandes' => Commnande::where('user_id', $user->user_id)->count(),
        'commandes_livrees' => Commnande::where('user_id', $user->user_id)->where('status', 'livree')->count(),
        'note_moyenne' => $user->evaluations()->avg('note') ?? 0,
        'montant_total' => Commnande::where('user_id', $user->user_id)->where('status', 'livree')->sum('prix_final')
    ];

    return view('client.dashboard', compact('commandeEnCours', 'livreursDisponibles', 'statistiques', 'livraisonsTerminees'));
}

   /**
 * API pour récupérer les données en temps réel
 */
public function apiDashboardData()
{
    $user = Auth::user();
    
    $commandeEnCours = Commnande::where('user_id', $user->user_id) 
        ->whereIn('status', ['acceptee', 'en_cours'])
        ->with(['driver.evaluationsClients']) 
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
        ->with('evaluationsClients') 
        ->limit(4) 
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
}public function marquerLivree(Request $request, $commandeId)
{
    try {
        // Valider les données d'entrée
        $validated = $request->validate([
            'commentaire_livraison' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo_livraison' => 'nullable|image|max:2048'
        ]);

        // Vérifier l'existence de la commande
        $commande = Commnande::where('id', $commandeId)
            ->where('driver_id', Auth::id())
            ->where('status', Commnande::STATUT_EN_COURS)
            ->first();

        if (!$commande) {
            Log::warning("Commande non trouvée ou non accessible", [
                'commande_id' => $commandeId,
                'driver_id' => Auth::id(),
                'status' => Commnande::STATUT_EN_COURS
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée ou non accessible.'
            ], 404);
        }

        // Vérifier les permissions du dossier de stockage
        $storagePath = storage_path('app/public/livraisons/' . date('Y/m'));
        if (!file_exists(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0755, true);
        }
        if (!is_writable(dirname($storagePath))) {
            Log::error("Dossier de stockage non accessible", [
                'path' => $storagePath
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur de configuration du serveur pour l\'upload.'
            ], 500);
        }

        // Gérer l'upload de la photo
        $photoPath = null;
        if ($request->hasFile('photo_livraison') && $request->file('photo_livraison')->isValid()) {
            try {
                $photoPath = $request->file('photo_livraison')->store('livraisons/' . date('Y/m'), 'public');
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'upload de la photo", [
                    'commande_id' => $commandeId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'upload de la photo.'
                ], 500);
            }
        }

        // Mettre à jour la commande dans une transaction
        \DB::beginTransaction();
        try {
            $commande->update([
                'status' => Commnande::STATUT_LIVREE,
                'date_livraison' => now(),
                'commentaire_livraison' => $validated['commentaire_livraison'],
                'photo_livraison' => $photoPath,
                'lat_livraison' => (float) $validated['latitude'],
                'lng_livraison' => (float) $validated['longitude']
            ]);

            // Mettre à jour la route de livraison
            $deliveryRoute = DeliveryRoute::where('commande_id', $commandeId)->first();
            if ($deliveryRoute) {
                $deliveryRoute->update([
                    'completed_at' => now(),
                    'final_position' => json_encode([
                        'lat' => (float) $validated['latitude'],
                        'lng' => (float) $validated['longitude']
                    ])
                ]);
            } else {
                Log::warning("Aucune DeliveryRoute trouvée pour la commande ID: {$commandeId}");
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error("Erreur lors de la mise à jour de la commande ou DeliveryRoute", [
                'commande_id' => $commandeId,
                'data' => $validated,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la commande.',
                'technical' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }

        Log::info("Livraison marquée comme livrée", [
            'commande_id' => $commandeId,
            'driver_id' => Auth::id(),
            'photo_path' => $photoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Livraison marquée comme livrée avec succès!',
            'commande' => $this->formatLivraisonData($commande)
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning("Erreur de validation dans marquerLivree", [
            'commande_id' => $commandeId,
            'errors' => $e->errors()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Données invalides.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error("Erreur serveur dans marquerLivree", [
            'commande_id' => $commandeId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Une erreur serveur est survenue.',
            'technical' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
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
                'note_moyenne' => round($commande->driver->evaluationsClients->avg('note') ?? 0, 1)

            ] : null,
            'created_at' => $commande->created_at->format('H:i'),
            'updated_at' => $commande->updated_at->format('H:i')
        ];
    }
    
    /**
     * Formater les données de livreur
     */
   /**
 * Formater les données de livreur
 */
private function formatLivreurData($livreur)
{
    $evaluations = $livreur->evaluationsClients ?? collect();
    $noteMoyenne = $evaluations->count() > 0 ? $evaluations->avg('note') : 0;
    
    return [
        'id' => $livreur->user_id, 
        'name' => $livreur->name,
        'photo' => $livreur->photo ?? 'https://i.pravatar.cc/60?img=' . rand(1, 10),
        'note_moyenne' => round($noteMoyenne, 1),
        'nombre_avis' => $evaluations->count(),
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

    if ($commande->user_id !== auth()->id()) {
        abort(403, 'Vous n’êtes pas autorisé à noter cette livraison.');
    }

    if (!$commande->driver) {
        return redirect()->back()->with('error', 'Cette livraison n’a pas de livreur assigné.');
    }

    $evaluationExistante = Evaluation::where('commande_id', $commande->id)
        ->where('user_id', auth()->id())  
        ->where('driver_id', $commande->driver->id)
        ->where('type_evaluation', 'client')
        ->first();

    if ($evaluationExistante) {
        return redirect()->back()->with('error', 'Vous avez déjà évalué ce livreur pour cette livraison.');
    }

    Evaluation::create([
        'commande_id' => $commande->id,
        'user_id' => auth()->id(),                
        'driver_id' => $commande->driver->id,     
        'note' => $request->rating,
        'commentaire' => $request->commentaire,
        'type_evaluation' => 'client',            
    ]);

    return redirect()->route('client.dashboard')->with('success', ' Merci pour votre évaluation !');
}


}
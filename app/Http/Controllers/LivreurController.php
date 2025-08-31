<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commnande;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\TrajetUrbain;
use App\Models\Evaluation;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;


class LivreurController extends Controller
{
    /**
     * Affiche les commandes disponibles pour les livreurs
     */
    public function commandesDisponibles(Request $request)
    {
        // Récupérer les commandes payées et non assignées à un livreur
        $query = Commnande::whereIn('status', Commnande::statutsDisponibles())
                      ->whereNull('driver_id')
                      ->with(['user']);
        
        // Filtrage selon le type de livreur
        $user = Auth::user();
        
        // Si le livreur est de type 'classique', filtrer par région Dakar uniquement
        if ($user->role === 'classique' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'classique')) {
            $query->where(function($q) {
                $q->where('region_arrivee', 'LIKE', '%Dakar%')
                  ->orWhere('adresse_arrivee', 'LIKE', '%Dakar%');
            });
        }
        // Si le livreur est de type 'urbain', filtrer pour exclure Dakar (seulement autres régions)
        elseif ($user->role === 'urbain' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'urbain')) {
            $query->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->where('region_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('region_arrivee');
                })->where(function($subQ) {
                    $subQ->where('adresse_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('adresse_arrivee');
                });
            });
        }
        
        // Filtres optionnels
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('adresse_depart', 'LIKE', "%{$search}%")
                  ->orWhere('adresse_arrivee', 'LIKE', "%{$search}%")
                  ->orWhere('reference', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type_colis')) {
            $query->where('type_colis', $request->type_colis);
        }

        if ($request->filled('type_livraison')) {
            $query->where('type_livraison', $request->type_livraison);
        }

        $query->orderBy('created_at', 'desc');

        $commandes = $query->paginate(10);

        // Statistiques ajustées selon le type de livreur
        $statsQuery = Commnande::whereIn('status', Commnande::statutsDisponibles())
                               ->whereNull('driver_id');
        
        if ($user->role === 'classique' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'classique')) {
            $statsQuery->where(function($q) {
                $q->where('region_arrivee', 'LIKE', '%Dakar%')
                  ->orWhere('adresse_arrivee', 'LIKE', '%Dakar%');
            });
        }
        // Si le livreur est de type 'urbain', filtrer pour exclure Dakar
        elseif ($user->role === 'urbain' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'urbain')) {
            $statsQuery->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->where('region_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('region_arrivee');
                })->where(function($subQ) {
                    $subQ->where('adresse_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('adresse_arrivee');
                });
            });
        }
        
        $stats = [
            'total_disponibles' => $statsQuery->count(),
            'total_acceptees' => Commnande::where('driver_id', Auth::id())
                                        ->whereIn('status', ['acceptee', 'en_cours'])
                                        ->count(),
            'revenus_jour' => Commnande::where('driver_id', Auth::id())
                                     ->where('status', 'livree')
                                     ->whereDate('updated_at', today())
                                     ->sum('prix_final')
        ];

        if ($request->ajax()) {
            return response()->json([
                'commandes' => $commandes,
                'stats' => $stats
            ]);
        }

        return view('livreur.livraisons-disponible', compact('commandes', 'stats'));
    }

    public function dashboarde()
    {
        $livreurId = Auth::id();
        $user = Auth::user();
        
        $livraisonActuelle = Commnande::where('driver_id', $livreurId)
            ->where('status', 'en_cours')
            ->with(['user'])
            ->first();
        
        // Filtrage des livraisons disponibles selon le type de livreur
        $livraisonsDisponiblesQuery = Commnande::whereIn('status', ['payee', 'confirmee'])
            ->whereNull('driver_id')
            ->with(['user']);
            
        // Si le livreur est de type 'classique', filtrer par région Dakar uniquement
        if ($user->role === 'classique' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'classique')) {
            $livraisonsDisponiblesQuery->where(function($q) {
                $q->where('region_arrivee', 'LIKE', '%Dakar%')
                  ->orWhere('adresse_arrivee', 'LIKE', '%Dakar%');
            });
        }
        // Si le livreur est de type 'urbain', filtrer pour exclure Dakar
        elseif ($user->role === 'urbain' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'urbain')) {
            $livraisonsDisponiblesQuery->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->where('region_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('region_arrivee');
                })->where(function($subQ) {
                    $subQ->where('adresse_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('adresse_arrivee');
                });
            });
        }
        
        $livraisonsDisponibles = $livraisonsDisponiblesQuery->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        $statistiques = [
            'livraisons_jour' => Commnande::where('driver_id', $livreurId)
                ->whereDate('created_at', today())
                ->count(),
            'revenus_jour' => Commnande::where('driver_id', $livreurId)
                ->where('status', 'livree')
                ->whereDate('updated_at', today())
                ->sum('prix_final') ?? 0,
            'en_cours' => Commnande::where('driver_id', $livreurId)
                ->where('status', 'en_cours')
                ->count(),
            
            'livraisons_completees' => Commnande::where('driver_id', $livreurId)
                ->where('status', 'livree')
                ->whereDate('updated_at', today())
                ->count(),
            'livraisons_total' => Commnande::where('driver_id', $livreurId)
                ->whereDate('updated_at', today())
                ->count(),
            'taux_completion' => $this->calculerTauxCompletion($livreurId),
            'note_moyenne' => $this->calculerNoteMoyenne($livreurId),
            'distance_jour' => 0, 
        ];
        
        return view('livreur.dashboarde', compact(
            'livraisonActuelle',
            'livraisonsDisponibles', 
            'statistiques'
        ));
    }

  public function showJson($id)
    {
    $livreur = \App\Models\Livreur::find($id);

    if (!$livreur) {
        return response()->json([
            'success' => false,
            'message' => 'Livreur introuvable'
        ], 404);
    }

    $livraisonsCount = \App\Models\Commnande::where('driver_id', $livreur->id)
        ->where('status', 'livree') 
        ->count();

    $ratingAvg = \App\Models\Evaluation::where('driver_id', $livreur->id)
        ->avg('note');

    return response()->json([
        'success' => true,
        'livreur' => [
            'id' => $livreur->id,
            'name' => $livreur->name,
            'email' => $livreur->email,
            'phone' => $livreur->numero_telephone,
            'is_active' => $livreur->is_active,
            'livraisons_count' => $livraisonsCount,
             'rating_avg' => round($livreur->rating_avg, 1) ?? 'Aucune', 

        ]
    ]);
}



    /**
     * Accepter une commande avec vérification de région pour livreurs classiques
     */


public function accepterCommande(Request $request, $commandeId)
{
    try {
        // Vérifier l'authentification
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        $user = Auth::user();

        // Log pour debug
        \Log::info('Tentative acceptation commande', [
            'commande_id' => $commandeId,
            'user_id' => $user->user_id,
            'user_role' => $user->role
        ]);

        // Rechercher la commande
        $commande = Commnande::find($commandeId);
        
        if (!$commande) {
            return response()->json([
                'success' => false,
                'message' => 'Commande introuvable.'
            ], 404);
        }

        // Vérifier que la commande n'est pas déjà acceptée
        if ($commande->driver_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande a déjà été acceptée par un autre livreur.'
            ], 400);
        }

        // Vérifier le statut de la commande
        if (!in_array($commande->status, Commnande::statutsAcceptables())) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande n\'est pas disponible. Statut: ' . $commande->status
            ], 400);
        }

        // Vérifier le rôle de l'utilisateur
        if (!in_array($user->role, ['livreur', 'classique', 'urbain'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à accepter des commandes. Rôle: ' . $user->role
            ], 403);
        }

        // Vérification des restrictions géographiques
        $regionArrivee = $commande->region_arrivee ?? '';
        $adresseArrivee = $commande->adresse_arrivee ?? '';
        
        $isDakar = stripos($regionArrivee, 'Dakar') !== false || 
                   stripos($adresseArrivee, 'Dakar') !== false;

        // Livreur classique : seulement Dakar
        if ($user->role === 'classique' || 
            ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'classique')) {
            
            if (!$isDakar) {
                return response()->json([
                    'success' => false,
                    'message' => 'En tant que livreur classique, vous ne pouvez accepter que des livraisons vers Dakar.'
                ], 403);
            }
        }
        // Livreur urbain : pas Dakar
        elseif ($user->role === 'urbain' || 
                ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'urbain')) {
            
            if ($isDakar) {
                return response()->json([
                    'success' => false,
                    'message' => 'En tant que livreur urbain, vous ne pouvez pas accepter des livraisons vers Dakar.'
                ], 403);
            }
        }

        // Accepter la commande
        $commande->driver_id = $user->user_id; // Utiliser user_id au lieu de Auth::id()
        $commande->status = Commnande::STATUT_ACCEPTEE;
        $commande->date_acceptation = now();
        
        $saved = $commande->save();

        if (!$saved) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde de la commande.'
            ], 500);
        }

        \Log::info('Commande acceptée avec succès', [
            'commande_id' => $commandeId,
            'driver_id' => $user->user_id
        ]);

        // Envoi de SMS (avec gestion d'erreur)
        try {
            if ($commande->user && !empty($commande->user->numero_telephone)) {
                $livreurNom = $user->name;
                $this->sendTwilioSMS(
                    $commande->user->numero_telephone,
                    "Votre commande #{$commande->reference} a été acceptée par {$livreurNom}. Il s'occupera de la livraison ! - ColisFast"
                );
            }
        } catch (\Exception $smsError) {
            // Log l'erreur SMS mais ne pas faire échouer la commande
            \Log::warning('Erreur envoi SMS', [
                'commande_id' => $commandeId,
                'error' => $smsError->getMessage()
            ]);
        }

        // Recharger la commande avec les relations
        $commande->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Commande acceptée avec succès!',
            'commande' => [
                'id' => $commande->id,
                'reference' => $commande->reference,
                'status' => $commande->status,
                'driver_id' => $commande->driver_id,
                'client_nom' => $commande->user->name ?? 'Non spécifié'
            ]
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Commande introuvable.'
        ], 404);
        
    } catch (\Illuminate\Database\QueryException $e) {
        \Log::error('Erreur base de données dans accepterCommande', [
            'commande_id' => $commandeId,
            'error' => $e->getMessage(),
            'sql' => $e->getSql() ?? 'N/A'
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur de base de données. Veuillez réessayer.'
        ], 500);
        
    } catch (\Exception $e) {
        \Log::error('Erreur générale dans accepterCommande', [
            'commande_id' => $commandeId,
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Une erreur inattendue s\'est produite. Veuillez réessayer.'
        ], 500);
    }
}



/**
 * Envoi de SMS via Twilio
 */
private function sendTwilioSMS($to, $message)
{
    try {
        $twilioSid = config('services.twilio.sid');
        $twilioToken = config('services.twilio.token');
        $twilioFrom = config('services.twilio.from');

        // Vérifier que le numéro de destination est valide
        if (empty($to)) {
            Log::warning('Numéro de téléphone manquant pour l\'envoi SMS');
            return;
        }

        $client = new Client($twilioSid, $twilioToken);

        $client->messages->create(
            $this->formatPhoneNumber($to), // Formater le numéro
            [
                'from' => $twilioFrom,
                'body' => $message
            ]
        );

        Log::info('SMS Twilio envoyé avec succès à: ' . $to);

    } catch (\Exception $e) {
        Log::error('Erreur Twilio SMS: ' . $e->getMessage());
        // Continuer sans interrompre le processus principal
    }
}

/**
 * Formater le numéro de téléphone pour Twilio (format international)
 */
private function formatPhoneNumber($phone)
{
    // Supprimer tous les caractères non numériques
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Si le numéro commence par 0, le convertir en format international
    if (substr($phone, 0, 1) === '0') {
        $phone = '+221' . substr($phone, 1);
    }
    // Si le numéro commence par 221, ajouter le +
    elseif (substr($phone, 0, 3) === '221') {
        $phone = '+' . $phone;
    }
    // Si le numéro n'a pas d'indicatif, ajouter +221 (Sénégal)
    elseif (strlen($phone) === 9) {
        $phone = '+221' . $phone;
    }
    
    return $phone;
}


    /**
     * API pour récupérer les commandes (pour mobile app) avec filtrage selon le type de livreur
     */
    public function apiCommandesDisponibles(Request $request)
    {
        $user = Auth::user();
        $query = Commnande::whereIn('status', ['payee', 'confirmee'])
                          ->whereNull('driver_id')
                          ->with(['user:id,name,phone']);

        // Filtrage selon le type de livreur pour l'API mobile
        if ($user->role === 'classique' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'classique')) {
            $query->where(function($q) {
                $q->where('region_arrivee', 'LIKE', '%Dakar%')
                  ->orWhere('adresse_arrivee', 'LIKE', '%Dakar%');
            });
        }
        // Si le livreur est de type 'urbain', filtrer pour exclure Dakar
        elseif ($user->role === 'urbain' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'urbain')) {
            $query->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->where('region_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('region_arrivee');
                })->where(function($subQ) {
                    $subQ->where('adresse_arrivee', 'NOT LIKE', '%Dakar%')
                         ->orWhereNull('adresse_arrivee');
                });
            });
        }

        if ($request->filled('lat') && $request->filled('lng')) {
            // Filtrer par proximité géographique
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->radius ?? 10; // 10km par défaut

            $query->selectRaw("
                *, 
                (6371 * acos(cos(radians(?)) * cos(radians(lat_depart)) * cos(radians(lng_depart) - radians(?)) + sin(radians(?)) * sin(radians(lat_depart)))) AS distance
            ", [$lat, $lng, $lat])
            ->having('distance', '<', $radius)
            ->orderBy('distance');
        }

        $commandes = $query->orderBy('created_at', 'desc')
                          ->limit(20)
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $commandes->map(function($commande) {
                return [
                    'id' => $commande->id,
                    'reference' => $commande->reference,
                    'adresse_depart' => $commande->adresse_depart,
                    'adresse_arrivee' => $commande->adresse_arrivee,
                    'region_arrivee' => $commande->region_arrivee ?? null,
                    'type_colis' => $commande->type_colis,
                    'type_livraison' => $commande->type_livraison,
                    'prix_final' => $commande->prix_final,
                    'distance' => $commande->distance ?? null,
                    'client' => [
                        'name' => $commande->user->name ?? 'Client',
                        'phone' => $commande->user->phone ?? null
                    ],
                    'created_at' => $commande->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Mes commandes en cours
     */
    public function mesCommandes()
    {
        $commandes = Commnande::where('driver_id', Auth::id())
                             ->whereIn('status', ['acceptee', 'en_cours', 'livree'])
                             ->with(['user'])
                             ->orderBy('updated_at', 'desc')
                             ->paginate(10);

        return view('livreur.commandes.mes-commandes', compact('commandes'));
    }

    /**
     * Détails d'une commande
     */
public function detailsCommande($id)
{
    $commande = Commnande::with('user')->find($id);

    if (!$commande) {
        return response()->json([
            'success' => false,
            'message' => 'Commande introuvable'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'commande' => $commande
    ]);
}



    /**
     * Statistiques du livreur
     */
    public function statistiques()
    {
        $livreurId = Auth::id();
        
        $stats = [
            'commandes_jour' => Commnande::where('driver_id', $livreurId)
                                        ->whereDate('date_livraison', today())
                                        ->count(),
            'commandes_semaine' => Commnande::where('driver_id', $livreurId)
                                           ->whereBetween('date_livraison', [now()->startOfWeek(), now()->endOfWeek()])
                                           ->count(),
            'commandes_mois' => Commnande::where('driver_id', $livreurId)
                                        ->whereMonth('date_livraison', now()->month)
                                        ->whereYear('date_livraison', now()->year)
                                        ->count(),
            'revenus_jour' => Commnande::where('driver_id', $livreurId)
                                      ->whereDate('date_livraison', today())
                                      ->sum('prix_final'),
            'revenus_semaine' => Commnande::where('driver_id', $livreurId)
                                         ->whereBetween('date_livraison', [now()->startOfWeek(), now()->endOfWeek()])
                                         ->sum('prix_final'),
            'revenus_mois' => Commnande::where('driver_id', $livreurId)
                                      ->whereMonth('date_livraison', now()->month)
                                      ->whereYear('date_livraison', now()->year)
                                      ->sum('prix_final'),
            'taux_completion' => $this->calculerTauxCompletion($livreurId),
            'note_moyenne' => $this->calculerNoteMoyenne($livreurId)
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Calculer le taux de completion
     */
    private function calculerTauxCompletion($livreurId)
    {
        $totalAcceptees = Commnande::where('driver_id', $livreurId)->count();
        $totalLivrees = Commnande::where('driver_id', $livreurId)
                                ->where('status', 'livree')
                                ->count();

        return $totalAcceptees > 0 ? round(($totalLivrees / $totalAcceptees) * 100, 2) : 0;
    }

    /**
     * Calculer la note moyenne (si vous avez un système de notation)
     */
    private function calculerNoteMoyenne($livreurId)
    {
        return Evaluation::where('driver_id', $livreurId)
                         ->where('type_evaluation', 'client')
                         ->avg('note') ?? 0;
    }

    public function index()
    {
        $livreurs = User::where('role', 'livreur')->paginate(10);
        return view('admin.livreurs.index', compact('livreurs'));
    }

    public function show($id)
    {
        $livreur = User::findOrFail($id);
        return view('admin.livreurs.show', compact('livreur'));
    }

    // Formulaire pour créer un trajet (seulement livreur urbain)
    public function createTrajet()
    {
        if (auth()->user()->type_livreur !== 'urbain') {
            abort(403, 'Accès interdit aux livreurs non urbains');
        }
        return view('livreur.trajets.create');
    }

    public function dasshboarde()
    {
        $trajets = TrajetUrbain::where('livreur_id', auth()->id())->get();
        $isUrbain = auth()->user()->type_livreur === 'urbain'; // Vérifier si le livreur est urbain
        return view('livreur.dashboarde', compact('trajets', 'isUrbain'));
    }

    public function voirCommandesAssignes($trajet_id)
    {
        // Vérifier que le livreur est de type urbain
        if (auth()->user()->type_livreur !== 'urbain') {
            abort(403, 'Accès interdit aux livreurs non urbains');
        }

        // Vérifier que le trajet appartient au livreur connecté
        $trajet = TrajetUrbain::where('id', $trajet_id)
                              ->where('livreur_id', auth()->id())
                              ->firstOrFail();

        // Récupérer les commandes assignées au livreur pour la même région
        $commandes = Commnande::where('driver_id', auth()->id())
                            ->where('region_arrivee', $trajet->destination_region)
                            ->with('user') // Charger les informations du client
                            ->get();

        return view('livreur.trajets.commandes', compact('trajet', 'commandes'));
    }

    public function destroy($id)
{
    $livreur = User::findOrFail($id);

    // Soft delete
    $livreur->delete();

    return redirect()->route('admin.livreurs.index')
                     ->with('success', 'Livreur supprimé avec succès.');
}


    // Sauvegarde du trajet
    public function storeTrajet(Request $request)
    {
        if (auth()->user()->type_livreur !== 'urbain') {
            abort(403, 'Accès interdit');
        }

        $request->validate([
            'type_voiture' => 'required|string',
            'matricule' => 'required|string',
            'heure_depart' => 'required',
            'destination_region' => 'required|string',
        ]);

        TrajetUrbain::create([
            'livreur_id' => auth()->id(),
            'type_voiture' => $request->type_voiture,
            'matricule' => $request->matricule,
            'heure_depart' => $request->heure_depart,
            'destination_region' => $request->destination_region,
        ]);

        return redirect()->route('livreur.dashboarde')->with('success', 'Trajet déclaré avec succès.');
    }

    // Voir ses trajets
    public function listeTrajets()
    {
        $trajets = TrajetUrbain::where('livreur_id', auth()->id())->latest()->get();
        return view('livreur.trajets.index', compact('trajets'));
    }
}
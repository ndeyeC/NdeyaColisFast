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

    /**
     * Accepter une commande avec vérification de région pour livreurs classiques
     */
    public function accepterCommande(Request $request, $commandeId)
    {
        $commande = Commnande::findOrFail($commandeId);
        $user = Auth::user();

        if ($commande->driver_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande a déjà été acceptée par un autre livreur.'
            ], 400);
        }

        if (!in_array($commande->status, Commnande::statutsAcceptables())) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande n\'est pas disponible.'
            ], 400);
        }

        if ($user->role !== 'livreur' && $user->role !== 'classique' && $user->role !== 'urbain') {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à accepter des commandes.'
            ], 403);
        }

        // Vérification spéciale pour les livreurs selon leur type
        if ($user->role === 'classique' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'classique')) {
            $isDakar = stripos($commande->region_arrivee, 'Dakar') !== false || 
                      stripos($commande->adresse_arrivee, 'Dakar') !== false;
            
            if (!$isDakar) {
                return response()->json([
                    'success' => false,
                    'message' => 'En tant que livreur classique, vous ne pouvez accepter que des livraisons vers Dakar.'
                ], 403);
            }
        }
        // Vérification pour les livreurs urbains (ne peuvent pas accepter les livraisons vers Dakar)
        elseif ($user->role === 'urbain' || ($user->role === 'livreur' && isset($user->type_livreur) && $user->type_livreur === 'urbain')) {
            $isDakar = stripos($commande->region_arrivee, 'Dakar') !== false || 
                      stripos($commande->adresse_arrivee, 'Dakar') !== false;
            
            if ($isDakar) {
                return response()->json([
                    'success' => false,
                    'message' => 'En tant que livreur urbain, vous ne pouvez pas accepter des livraisons vers Dakar.'
                ], 403);
            }
        }

        $commande->driver_id = Auth::id();
        $commande->status = 'acceptee';
        $commande->date_acceptation = now();
        $commande->save();

        $livreurNom = Auth::user()->name;
        $this->sendPushNotification(
            $commande->user->fcm_token, 
            "Commande acceptée", 
            "Votre commande a été acceptée par **$livreurNom**. Il s'occupera de la livraison !"
        );

        return response()->json([
            'success' => true,
            'message' => 'Commande acceptée avec succès!',
            'commande' => $commande->load('user')
        ]);
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

        // Filtres géographiques
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
    public function detailsCommande($commandeId)
    {
        $commande = Commnande::with(['user'])
                            ->findOrFail($commandeId);

        // Vérifier l'autorisation
        if ($commande->driver_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Non autorisé');
        }

        return view('livreur.commandes.details', compact('commande'));
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
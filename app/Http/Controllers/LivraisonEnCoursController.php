<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Commnande;
use App\Models\DeliveryRoute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class LivraisonEnCoursController extends Controller
{
    /**
     * Afficher la page des livraisons en cours
     */
    public function index()
    {
        $livreurId = Auth::id();
        
        $livraisonActuelle = Commnande::where('driver_id', $livreurId)
            ->where('status', 'en_cours')
            ->with(['user', 'deliveryRoute'])
            ->first();
        
        $livraisonsEnAttente = Commnande::where('driver_id', $livreurId)
            ->where('status', 'acceptee')
            ->with(['user'])
            ->get();
        
        $statistiques = $this->getStatistiquesJour($livreurId);

        // Calculer le pourcentage de progression
        $progressPercentage = 0;
        if ($livraisonActuelle && $livraisonActuelle->deliveryRoute) {
            $progressPercentage = $this->calculateProgress($livraisonActuelle->deliveryRoute);
        }

        return view('livreur.livraison-cours', compact(
            'livraisonActuelle',
            'livraisonsEnAttente', 
            'statistiques',
            'progressPercentage'
        ));
    }

    /**
     * API pour récupérer les livraisons en cours (pour AJAX)
     */
    public function apiLivraisonsEnCours()
    {
        $livreurId = Auth::id();
        
        $livraisonActuelle = Commnande::where('driver_id', $livreurId)
            ->where('status', 'en_cours')
            ->with(['user', 'deliveryRoute'])
            ->first();
        
        $livraisonsEnAttente = Commnande::where('driver_id', $livreurId)
            ->where('status', 'acceptee')
            ->with(['user'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        $data = [
            'livraison_actuelle' => $livraisonActuelle ? $this->formatLivraisonData($livraisonActuelle) : null,
            'livraisons_en_attente' => $livraisonsEnAttente->map(function($livraison) {
                return $this->formatLivraisonData($livraison);
            }),
            'statistiques' => $this->getStatistiquesJour($livreurId)
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Démarrer une livraison
     */
    public function demarrerLivraison(Request $request, $commandeId)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $commande = Commnande::where('id', $commandeId)
            ->where('driver_id', Auth::id())
            ->where('status', 'acceptee')
            ->firstOrFail();

        // Vérifier qu'il n'y a pas déjà une livraison en cours
        $livraisonEnCours = Commnande::where('driver_id', Auth::id())
            ->where('status', 'en_cours')
            ->exists();

        if ($livraisonEnCours) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà une livraison en cours. Terminez-la avant d\'en commencer une nouvelle.'
            ], 400);
        }

        // Mettre à jour le statut de la commande
        $commande->status = 'en_cours';
        $commande->date_debut_livraison = now();
        $commande->save();

        // Créer ou mettre à jour la route de livraison
        $route = DeliveryRoute::updateOrCreate(
            ['commande_id' => $commandeId],
            [
                'driver_id' => Auth::id(),
                'start_point' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude
                ],
                'end_point' => [
                    'lat' => $commande->lat_arrivee,
                    'lng' => $commande->lng_arrivee
                ],
                'started_at' => now(),
                'current_position' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude
                ]
            ]
        );

        $this->calculateRoute($route);

        return response()->json([
            'success' => true,
            'message' => 'Livraison démarrée avec succès!',
            'commande' => $this->formatLivraisonData($commande->load(['user', 'deliveryRoute']))
        ]);
    }

    /**
     * Mettre à jour la position du livreur
     */
    public function updatePosition(Request $request, $commandeId)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $commande = Commnande::where('id', $commandeId)
            ->where('driver_id', Auth::id())
            ->where('status', 'en_cours')
            ->firstOrFail();

        DeliveryRoute::where('commande_id', $commandeId)
            ->update([
                'current_position' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude
                ],
                'updated_at' => now()
            ]);

        $route = $commande->deliveryRoute;
        $distanceRestante = null;
        $tempsEstime = null;

        if ($route && $route->end_point) {
            $distanceRestante = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $route->end_point['lat'],
                $route->end_point['lng']
            );
            
            $tempsEstime = round(($distanceRestante / 30) * 60); // en minutes
        }

        return response()->json([
            'success' => true,
            'distance_restante' => $distanceRestante,
            'temps_estime' => $tempsEstime,
            'position_updated' => true
        ]);
    }

    /**
     * Marquer une livraison comme livrée
     */
    public function marquerLivree(Request $request, $commandeId)
    {
        $request->validate([
            'code_confirmation' => 'sometimes|string|max:10',
            'commentaire' => 'nullable|string|max:500',
            'photo_livraison' => 'sometimes|image|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $commande = Commnande::where('id', $commandeId)
            ->where('driver_id', Auth::id())
            ->where('status', 'en_cours')
            ->firstOrFail();

        // Ajouter la position actuelle si pas fournie
        if (!$request->has('latitude') || !$request->has('longitude')) {
            return response()->json([
                'success' => false,
                'message' => 'Position requise pour marquer la livraison'
            ], 400);
        }

        // Gérer l'upload de photo si présente
        $photoPath = null;
        if ($request->hasFile('photo_livraison')) {
            $photoPath = $request->file('photo_livraison')->store('livraisons/' . date('Y/m'), 'public');
        }

        $commande->update([
            'status' => 'livree',
            'date_livraison' => now(),
            'commentaire_livraison' => $request->commentaire,
            'photo_livraison' => $photoPath,
            'lat_livraison' => $request->latitude,
            'lng_livraison' => $request->longitude
        ]);

        DeliveryRoute::where('commande_id', $commandeId)
            ->update([
                'completed_at' => now(),
                'final_position' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude
                ]
            ]);

        // Log de l'activité
        Log::info("Livraison terminée", [
            'commande_id' => $commandeId,
            'driver_id' => Auth::id(),
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Livraison marquée comme livrée avec succès!',
            'commande' => $this->formatLivraisonData($commande)
        ]);
    }

    /**
     * Signaler un problème
     */
//  public function signalerProbleme(Request $request, $commandeId)
// {
//     $request->validate([
//         'type_probleme' => 'required|string|in:client_absent,adresse_incorrecte,colis_endommage,autre',
//         'description' => 'required|string|max:500',
//         'photo' => 'sometimes|image|max:2048'
//     ]);

//     // Récupérez l'ID de l'utilisateur connecté
//     $driverId = Auth::id();
    
//     // Journalisation pour débogage
//     \Log::info('Tentative de signalement', [
//         'commande_id' => $commandeId,
//         'driver_id' => $driverId,
//         'time' => now()
//     ]);

//     // Vérification en 3 étapes avec messages d'erreur spécifiques
//     $commande = Commnande::where('id', $commandeId)->first();

//     if (!$commande) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Commande introuvable',
//             'debug' => [
//                 'commande_exists' => false,
//                 'driver_id' => $driverId
//             ]
//         ], 404);
//     }

//     if ($commande->driver_id != $driverId) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Cette commande ne vous est pas attribuée',
//             'debug' => [
//                 'expected_driver' => $driverId,
//                 'actual_driver' => $commande->driver_id
//             ]
//         ], 403);
//     }

//     if (!in_array($commande->status, ['en_cours', 'acceptee'])) {
//         return response()->json([
//             'success' => false,
//             'message' => 'La commande n\'est pas dans un état permettant de signaler un problème',
//             'current_status' => $commande->status
//         ], 400);
//     }

//     // Traitement du signalement
//     $photoPath = $request->hasFile('photo') 
//         ? $request->file('photo')->store('problemes/' . date('Y/m'), 'public')
//         : null;

//     $probleme = [
//         'commande_id' => $commandeId,
//         'driver_id' => $driverId,
//         'type_probleme' => $request->type_probleme,
//         'description' => $request->description,
//         'photo' => $photoPath,
//         'date_signalement' => now(),
//         'status' => 'en_attente'
//     ];

//     $commande->update([
//         'probleme_signale' => json_encode($probleme),
//         'status' => 'probleme_signale'
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Problème signalé avec succès!',
//         'data' => [
//             'problem_id' => $commandeId,
//             'new_status' => 'probleme_signale',
//             'photo_uploaded' => !is_null($photoPath)
//         ]
//     ]);
// }

public function signalerProbleme(Request $request, $commandeId)
{
    $validated = $request->validate([
        'type_probleme' => 'required|string|in:client_absent,adresse_incorrecte,colis_endommage,autre',
        'description' => 'required|string|max:500',
        'photo' => 'sometimes|image|max:2048'
    ]);

    DB::beginTransaction();
    try {
        $commande = Commnande::where('id', $commandeId)
                   ->where('driver_id', Auth::id())
                   ->whereIn('status', ['en_cours', 'acceptee'])
                   ->lockForUpdate() // Verrouille la ligne
                   ->firstOrFail();

        $photoPath = $request->hasFile('photo') 
            ? $request->file('photo')->store('problemes/'.date('Y/m'), 'public')
            : null;

        $problemeData = [
            'type' => $validated['type_probleme'],
            'description' => $validated['description'],
            'photo' => $photoPath,
            'date' => now()->toISOString(),
            'status' => 'en_attente',
            'ip' => $request->ip()
        ];

        $commande->forceFill([
            'probleme_signale' => $problemeData, 
            'status' => 'probleme_signale'
        ])->save();

        // VÉRIFICATION IMMÉDIATE
        $savedData = $commande->fresh();
        if (empty($savedData->probleme_signale)) {
            throw new \Exception("Échec de persistance des données");
        }

        DB::commit();

     return response()->json([
    'success' => true,
    'saved_data' => $savedData->probleme_signale,
    'alert' => [
        'title' => 'Signalement enregistré',
        'message' => 'Le problème a été signalé avec succès',
        'type' => 'success'
    ]
]);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Échec signalement", [
            'error' => $e->getMessage(),
            'commande' => $commandeId,
            'data' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'technical' => config('app.debug') ? $e->getMessage() : null,
            'message' => "Échec de l'enregistrement"
        ], 500);
    }
}

    /**
     * Annuler une livraison
     */
    public function annulerLivraison(Request $request, $commandeId)
    {
        $request->validate([
            'raison' => 'required|string|max:500'
        ]);

        $commande = Commnande::where('id', $commandeId)
            ->where('driver_id', Auth::id())
            ->whereIn('status', ['acceptee', 'en_cours'])
            ->firstOrFail();

        $commande->update([
            'driver_id' => null,
            'status' => 'payee', 
            'raison_annulation' => $request->raison,
            'date_annulation' => now()
        ]);

        DeliveryRoute::where('commande_id', $commandeId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Livraison annulée. Elle est maintenant disponible pour d\'autres livreurs.'
        ]);
    }

    /**
     * Ouvrir la navigation
     */
    public function ouvrirNavigation($commandeId)
    {
        $commande = Commnande::where('id', $commandeId)
            ->where('driver_id', Auth::id())
            ->where('status', 'en_cours')
            ->with('deliveryRoute')
            ->firstOrFail();

        $route = $commande->deliveryRoute;
        
        if (!$route) {
            return response()->json([
                'success' => false,
                'message' => 'Route non trouvée'
            ], 404);
        }

        // URLs pour différentes applications de navigation
        $navigationUrls = [
            'google_maps' => "https://www.google.com/maps/dir/{$route->start_point['lat']},{$route->start_point['lng']}/{$route->end_point['lat']},{$route->end_point['lng']}",
            'waze' => "https://waze.com/ul?ll={$route->end_point['lat']},{$route->end_point['lng']}&navigate=yes",
            'apple_maps' => "http://maps.apple.com/?daddr={$route->end_point['lat']},{$route->end_point['lng']}&dirflg=d"
        ];

        return response()->json([
            'success' => true,
            'navigation_urls' => $navigationUrls,
            'route_data' => $route
        ]);
    }

    /**
     * Calculer l'itinéraire avec OSRM
     */
    private function calculateRoute(DeliveryRoute $route)
    {
        try {
            $start = $route->start_point['lng'] . ',' . $route->start_point['lat'];
            $end = $route->end_point['lng'] . ',' . $route->end_point['lat'];

            $response = Http::timeout(10)->get("http://router.project-osrm.org/route/v1/driving/{$start};{$end}", [
                'overview' => 'full',
                'steps' => 'true',
                'geometries' => 'geojson'
            ]);

            if ($response->successful()) {
                $data = $response->json()['routes'][0];
                $route->update([
                    'polyline' => $data['geometry'],
                    'steps' => $data['legs'][0]['steps'] ?? [],
                    'distance_km' => round($data['distance'] / 1000, 2),
                    'duration_minutes' => round($data['duration'] / 60)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur calcul route OSRM: ' . $e->getMessage());
        }
    }

    /**
     * Calculer la distance entre deux points (en km)
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Rayon de la Terre en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Formater les données de livraison pour l'API
     */
    private function formatLivraisonData($commande)
    {
        $route = $commande->deliveryRoute;
        
        return [
            'id' => $commande->id,
            'reference' => $commande->reference,
            'status' => $commande->status,
            'type_colis' => $commande->type_colis,
            'type_livraison' => $commande->type_livraison,
            'prix_final' => $commande->prix_final,
            'adresse_depart' => $commande->adresse_depart,
            'adresse_arrivee' => $commande->adresse_arrivee,
            'details_adresse_depart' => $commande->details_adresse_depart,
            'details_adresse_arrivee' => $commande->details_adresse_arrivee,
            'date_acceptation' => $commande->date_acceptation,
            'date_debut_livraison' => $commande->date_debut_livraison,
            'client' => [
                'name' => $commande->user->name ?? 'Client',
                'phone' => $commande->user->phone ?? null
            ],
            'route' => $route ? [
                'distance_km' => $route->distance_km,
                'duration_minutes' => $route->duration_minutes,
                'current_position' => $route->current_position,
                'progress_percentage' => $this->calculateProgress($route),
                'polyline' => $route->polyline,
                'steps' => $route->steps
            ] : null
        ];
    }

    /**
     * Calculer le pourcentage de progression
     */
    private function calculateProgress($route)
    {
        if (!$route || !$route->current_position || !$route->start_point || !$route->end_point) {
            return 0;
        }

        $totalDistance = $this->calculateDistance(
            $route->start_point['lat'],
            $route->start_point['lng'],
            $route->end_point['lat'],
            $route->end_point['lng']
        );

        $distanceRestante = $this->calculateDistance(
            $route->current_position['lat'],
            $route->current_position['lng'],
            $route->end_point['lat'],
            $route->end_point['lng']
        );

        if ($totalDistance == 0) return 100;

        $progress = (($totalDistance - $distanceRestante) / $totalDistance) * 100;
        return max(0, min(100, round($progress)));
    }

    /**
     * Récupérer les statistiques du jour
     */
    private function getStatistiquesJour($livreurId)
    {
        return [
            'livraisons_jour' => Commnande::where('driver_id', $livreurId)
                ->whereDate('date_livraison', today())
                ->count(),
            'revenus_jour' => Commnande::where('driver_id', $livreurId)
                ->where('status', 'livree')
                ->whereDate('date_livraison', today())
                ->sum('prix_final'),
            'en_cours' => Commnande::where('driver_id', $livreurId)
                ->where('status', 'en_cours')
                ->count(),
            'en_attente' => Commnande::where('driver_id', $livreurId)
                ->where('status', 'acceptee')
                ->count()
        ];
    }
}
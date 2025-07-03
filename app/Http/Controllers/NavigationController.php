<?php

namespace App\Http\Controllers;

use App\Models\Commnande;
use App\Models\DeliveryRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NavigationController extends Controller
{
    public function showNavigation($commandeId)
    {
        try {
            // Debug: Log de l'ID reçu
            Log::info("Navigation demandée pour commande ID: " . $commandeId);
            
            $commande = Commnande::findOrFail($commandeId);
            
            // Debug: Log des données de commande
            Log::info("Commande trouvée:", [
                'id' => $commande->id,
                'adresse_depart' => $commande->adresse_depart ?? 'N/A',
                'adresse_arrivee' => $commande->adresse_arrivee ?? 'N/A',
                'lat_arrivee' => $commande->lat_arrivee ?? 'N/A',
                'lng_arrivee' => $commande->lng_arrivee ?? 'N/A'
            ]);
            
            $route = DeliveryRoute::where('commande_id', $commandeId)->first();

            // Si une route existe, la formater pour le frontend
            $formattedRoute = null;
            if ($route) {
                $formattedRoute = [
                    'start_point' => $route->start_point,
                    'end_point' => $route->end_point,
                    'start_address' => $commande->adresse_depart,
                    'end_address' => $commande->adresse_arrivee,
                    'distance_km' => $route->distance_km,
                    'duration_minutes' => $route->duration_minutes,
                    'steps' => $this->formatSteps($route->steps ?? [])
                ];
                
                Log::info("Route formatée:", $formattedRoute);
            }

            return view('livreur.navigation', [
                'commande' => $commande,
                'route' => $formattedRoute // Changed from $formattedRoute to ensure consistency
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erreur dans showNavigation: " . $e->getMessage());
            abort(404, "Commande non trouvée");
        }
    }

    public function getRouteData($commandeId)
    {
        try {
            Log::info("Route data demandée pour commande ID: " . $commandeId);
            
            $commande = Commnande::findOrFail($commandeId);
            $route = DeliveryRoute::where('commande_id', $commandeId)->first();

            if (!$route) {
                return response()->json([
                    'error' => 'Aucune route trouvée pour cette commande'
                ], 404);
            }

            $response = [
                'start_point' => $route->start_point,
                'end_point' => $route->end_point,
                'start_address' => $commande->adresse_depart,
                'end_address' => $commande->adresse_arrivee,
                'distance_km' => $route->distance_km,
                'duration_minutes' => $route->duration_minutes,
                'steps' => $this->formatSteps($route->steps ?? [])
            ];
            
            Log::info("Route data response:", $response);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error("Erreur dans getRouteData: " . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function startTracking(Request $request, $commandeId)
    {
        try {
            Log::info("Start tracking pour commande ID: " . $commandeId, $request->all());
            
            $request->validate([
                'current_lat' => 'required|numeric',
                'current_lng' => 'required|numeric'
            ]);

            $commande = Commnande::findOrFail($commandeId);

            // Vérifier que la commande a les coordonnées de destination
            if (!$commande->lat_arrivee || !$commande->lng_arrivee) {
                Log::warning("Coordonnées de destination manquantes pour commande " . $commandeId);
                return response()->json([
                    'error' => 'Coordonnées de destination manquantes pour cette commande'
                ], 400);
            }

            $route = DeliveryRoute::updateOrCreate(
                ['commande_id' => $commandeId],
                [
                    'driver_id' => auth()->id(),
                    'start_point' => [
                        'lat' => (float)$request->current_lat,
                        'lng' => (float)$request->current_lng
                    ],
                    'end_point' => [
                        'lat' => (float)$commande->lat_arrivee,
                        'lng' => (float)$commande->lng_arrivee
                    ],
                    'current_position' => [
                        'lat' => (float)$request->current_lat,
                        'lng' => (float)$request->current_lng
                    ],
                    'started_at' => now()
                ]
            );

            Log::info("Route créée/mise à jour:", [
                'route_id' => $route->id,
                'start_point' => $route->start_point,
                'end_point' => $route->end_point
            ]);

            // Calculer la route avec OSRM
            $routeCalculated = $this->calculateOsrmRoute($route);

            if (!$routeCalculated) {
                Log::error("Échec du calcul de route OSRM pour commande " . $commandeId);
                return response()->json([
                    'error' => 'Impossible de calculer l\'itinéraire. Veuillez réessayer.'
                ], 500);
            }

            // Recharger la route pour avoir les données mises à jour
            $route->refresh();

            $response = [
                'start_point' => $route->start_point,
                'end_point' => $route->end_point,
                'start_address' => $commande->adresse_depart,
                'end_address' => $commande->adresse_arrivee,
                'distance_km' => $route->distance_km,
                'duration_minutes' => $route->duration_minutes,
                'steps' => $this->formatSteps($route->steps ?? [])
            ];
            
            Log::info("Start tracking response:", $response);
            
            return response()->json($response);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Erreur de validation dans startTracking:", $e->errors());
            return response()->json(['error' => 'Données de géolocalisation invalides'], 422);
        } catch (\Exception $e) {
            Log::error("Erreur dans startTracking: " . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur lors du démarrage'], 500);
        }
    }

    /**
     * Format steps data for frontend consumption
     */
    private function formatSteps($steps)
    {
        if (!is_array($steps)) {
            return [];
        }

        return array_map(function($step) {
            return [
                'instruction' => $step['maneuver']['instruction'] ?? 'Continuer',
                'distance' => round($step['distance'] ?? 0),
                'duration' => round(($step['duration'] ?? 0) / 60, 1)
            ];
        }, $steps);
    }
   
    private function calculateOsrmRoute(DeliveryRoute $route)
    {
        try {
            $start = $route->start_point['lng'].','.$route->start_point['lat'];
            $end = $route->end_point['lng'].','.$route->end_point['lat'];

            $response = Http::timeout(10)->get("http://router.project-osrm.org/route/v1/driving/{$start};{$end}", [
                'overview' => 'full',
                'steps' => 'true',
                'geometries' => 'geojson'
            ]);

            if ($response->successful() && isset($response->json()['routes'][0])) {
                $data = $response->json()['routes'][0];
                $route->update([
                    'polyline' => $data['geometry'],
                    'steps' => $data['legs'][0]['steps'] ?? [],
                    'distance_km' => round(($data['distance'] ?? 0) / 1000, 2),
                    'duration_minutes' => round(($data['duration'] ?? 0) / 60)
                ]);
                
                Log::info("Route OSRM calculée avec succès");
                return true;
            } else {
                Log::error("Réponse OSRM invalide", ['response' => $response->json()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Erreur calcul route OSRM: " . $e->getMessage());
            return false;
        }
    }

    public function updatePosition(Request $request, $commandeId)
    {
        try {
            $validated = $request->validate([
                'lat' => 'required|numeric',
                'lng' => 'required|numeric'
            ]);

            DeliveryRoute::where('commande_id', $commandeId)
                ->update(['current_position' => $validated]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("Erreur updatePosition: " . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function completeDelivery($commandeId)
    {
        try {
            Commnande::where('id', $commandeId)->update(['status' => 'livrée']);
            DeliveryRoute::where('commande_id', $commandeId)
                ->update(['completed_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Erreur completeDelivery: " . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
}
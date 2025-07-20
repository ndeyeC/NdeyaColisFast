<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function geocode($adresse, $detailsAdresse = null, $useORS = true)
    {
        try {
            // Combiner l'adresse principale et les détails si disponibles
            $adresseFormatee = str_contains(strtolower($adresse), 'senegal')
                ? $adresse
                : ($detailsAdresse ? "$detailsAdresse, $adresse, Senegal" : "$adresse, Senegal");

            // Configurer le client HTTP
            $httpClient = Http::withOptions([
                'verify' => app()->environment('local') ? false : true,
            ])->withHeaders([
                'User-Agent' => 'ColisFastApp/1.0 (contact: votre-email@example.com)',
            ]);

            if ($useORS) {
                // Utiliser l'API de géocodage d'ORS
                $response = $httpClient->withHeaders([
                    'Authorization' => env('ORS_API_KEY'),
                ])->get("https://api.openrouteservice.org/geocode/search", [
                    'text' => $adresseFormatee,
                    'size' => 1,
                    'boundary.country' => 'SEN',
                    'layers' => 'address,locality', // Inclure les adresses et localités
                ]);

                if ($response->successful() && !empty($response->json()['features'])) {
                    $result = $response->json()['features'][0]['geometry']['coordinates'];
                    Log::info("Géocodage ORS réussi pour l'adresse: {$adresseFormatee}", [
                        'original' => $adresse,
                        'details' => $detailsAdresse,
                        'lat' => $result[1],
                        'lon' => $result[0]
                    ]);
                    return [
                        'lat' => (float) $result[1],
                        'lon' => (float) $result[0]
                    ];
                } else {
                    Log::warning("Échec du géocodage ORS pour l'adresse: {$adresseFormatee}", [
                        'original' => $adresse,
                        'details' => $detailsAdresse,
                        'response' => $response->json() ?? 'Réponse vide',
                        'status' => $response->status()
                    ]);
                    // Revenir à Nominatim si ORS échoue
                    return $this->geocodeWithNominatim($adresseFormatee, $adresse, $detailsAdresse);
                }
            } else {
                return $this->geocodeWithNominatim($adresseFormatee, $adresse, $detailsAdresse);
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors du géocodage de l'adresse: {$adresseFormatee}", [
                'original' => $adresse,
                'details' => $detailsAdresse,
                'api' => $useORS ? 'ORS' : 'Nominatim',
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function geocodeWithNominatim($adresseFormatee, $adresse, $detailsAdresse)
    {
        try {
            $httpClient = Http::withOptions([
                'verify' => app()->environment('local') ? false : true,
            ])->withHeaders([
                'User-Agent' => 'ColisFastApp/1.0 (contact: votre-email@example.com)',
            ]);

            $response = $httpClient->get("https://nominatim.openstreetmap.org/search", [
                'q' => $adresseFormatee,
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'SN',
                'viewbox' => '-17.5,14.6,-17.3,14.8',
                'bounded' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $result = $response->json()[0];
                Log::info("Géocodage Nominatim réussi pour l'adresse: {$adresseFormatee}", [
                    'original' => $adresse,
                    'details' => $detailsAdresse,
                    'lat' => $result['lat'],
                    'lon' => $result['lon']
                ]);
                return [
                    'lat' => (float) $result['lat'],
                    'lon' => (float) $result['lon']
                ];
            }

            Log::warning("Échec du géocodage Nominatim pour l'adresse: {$adresseFormatee}", [
                'original' => $adresse,
                'details' => $detailsAdresse,
                'response' => $response->json() ?? 'Réponse vide',
                'status' => $response->status()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("Erreur lors du géocodage Nominatim de l'adresse: {$adresseFormatee}", [
                'original' => $adresse,
                'details' => $detailsAdresse,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
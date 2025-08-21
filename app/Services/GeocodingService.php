<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    private function simplifyAddress($adresse, $detailsAdresse = null)
    {
        // Supprimer les mots ambigus pour simplifier la recherche
        $stopWords = ['grande', 'mosquée', 'mosquee', 'rue', 'avenue', 'bd', 'boulevard'];
        $adresse = trim(strtolower($adresse));
        $detailsAdresse = $detailsAdresse ? trim(strtolower($detailsAdresse)) : null;

        foreach ($stopWords as $word) {
            $adresse = str_replace($word, '', $adresse);
            if ($detailsAdresse) {
                $detailsAdresse = str_replace($word, '', $detailsAdresse);
            }
        }

        $adresse = preg_replace('/\s+/', ' ', trim($adresse));
        if ($detailsAdresse) {
            $detailsAdresse = preg_replace('/\s+/', ' ', trim($detailsAdresse));
        }

        return [$adresse, $detailsAdresse];
    }

    public function geocode($adresse, $detailsAdresse = null, $useORS = false)
    {
        try {
            // Valider l'adresse
            if (empty(trim($adresse))) {
                Log::warning("Adresse vide fournie pour le géocodage", [
                    'original' => $adresse,
                    'details' => $detailsAdresse
                ]);
                return [
                    'success' => false,
                    'message' => "Adresse vide. Vérifiez l'orthographe ou utilisez un quartier connu comme Plateau, Maristes, ou Castor."
                ];
            }

            // Simplifier l'adresse pour gérer les erreurs d'orthographe
            [$adresse, $detailsAdresse] = $this->simplifyAddress($adresse, $detailsAdresse);

            // Combiner l'adresse principale et les détails
            $adresseFormatee = str_contains(strtolower($adresse), 'senegal')
                ? $adresse
                : ($detailsAdresse ? "$detailsAdresse, $adresse, Senegal" : "$adresse, Senegal");

            $httpClient = Http::withOptions([
                'verify' => app()->environment('local') ? false : true,
            ])->withHeaders([
                'User-Agent' => 'ColisFastApp/1.0 (contact: votre-email@example.com)',
            ]);

            // Essayer Nominatim avec l'adresse complète
            $result = $this->geocodeWithNominatim($adresseFormatee, $adresse, $detailsAdresse);
            if ($result['success']) {
                return $result;
            }

            // Essayer une recherche partielle avec l'adresse principale seule
            if ($detailsAdresse) {
                Log::info("Tentative de géocodage partiel avec: {$adresse}, Senegal");
                $result = $this->geocodeWithNominatim("$adresse, Senegal", $adresse, null);
                if ($result['success']) {
                    return $result;
                }
            }

            // Essayer une recherche encore plus large (ville seule)
            Log::info("Tentative de géocodage avec ville seule: Dakar, Senegal");
            $result = $this->geocodeWithNominatim("Dakar, Senegal", "Dakar", null);
            if ($result['success']) {
                return $result;
            }

            // Échec total
            return [
                'success' => false,
                'message' => "Impossible de géocoder l'adresse: {$adresseFormatee}. Vérifiez l'orthographe ou utilisez un quartier connu comme Plateau, Maristes, ou Castor."
            ];
        } catch (\Exception $e) {
            Log::error("Erreur lors du géocodage de l'adresse: {$adresseFormatee}", [
                'original' => $adresse,
                'details' => $detailsAdresse,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => "Erreur lors du géocodage: {$e->getMessage()}. Vérifiez l'orthographe ou utilisez un quartier connu comme Plateau, Maristes, ou Castor."
            ];
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
                    'success' => true,
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
            return [
                'success' => false,
                'message' => "Nominatim n'a pas pu géocoder l'adresse: {$adresseFormatee}. Vérifiez l'orthographe ou utilisez un quartier connu comme Plateau, Maristes, ou Castor."
            ];
        } catch (\Exception $e) {
            Log::error("Erreur lors du géocodage Nominatim de l'adresse: {$adresseFormatee}", [
                'original' => $adresse,
                'details' => $detailsAdresse,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => "Erreur Nominatim: {$e->getMessage()}. Vérifiez l'orthographe ou utilisez un quartier connu comme Plateau, Maristes, ou Castor."
            ];
        }
    }
}
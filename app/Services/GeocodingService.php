<?php

// app/Services/GeocodingService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public function geocode($adresse)
    {
        $response = Http::get("https://nominatim.openstreetmap.org/search", [
            'q' => $adresse,
            'format' => 'json',
            'limit' => 1
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            return [
                'lat' => $response->json()[0]['lat'],
                'lon' => $response->json()[0]['lon']
            ];
        }

        return null;
    }
}

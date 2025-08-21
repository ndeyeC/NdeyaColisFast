<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = [
        'zone', 'type_livraison', 'tranche_distance', 
        'tranche_poids', 'prix', 'type_zone' 
    ];

    public static function distancesOptions()
    {
        return [
            '0-10 km' => '0-10 km',
            '10-50 km' => '10-50 km',
            '50-100 km' => '50-100 km',
            '100+ km' => '100+ km'
        ];
    }

    public static function poidsOptions()
    {
        return [
            '0-5 kg' => '0-5 kg',
            '5-20 kg' => '5-20 kg',
            '20-50 kg' => '20-50 kg',
            '50+ kg' => '50+ kg'
        ];
    }

    public static function typeZoneOptions()
    {
        return [
            'intra_urbaine' => 'Intra-urbaine',
            'region_proche' => 'Région proche',
            'region_eloignee' => 'Région éloignée',
        ];
    }

    public static function findTarif($regionDepart, $regionArrivee, $serviceType, $weightRange)
    {
        // 1. Trouver la zone correspondante (vérifier A → B ou B → A)
        $zone = Zone::where(function ($query) use ($regionDepart, $regionArrivee) {
            $query->where('region_depart', $regionDepart)
                  ->where('region_arrivee', $regionArrivee)
                  ->orWhere(function ($query) use ($regionDepart, $regionArrivee) {
                      $query->where('region_depart', $regionArrivee)
                            ->where('region_arrivee', $regionDepart);
                  });
        })->first();

        if (!$zone) {
            \Log::info("Aucune zone trouvée pour {$regionDepart} → {$regionArrivee}");
            return null;
        }

        $serviceType = strtolower($serviceType);

        // 2. Trouver le tarif correspondant
        $tarif = self::where('zone', $zone->type_zone)
                     ->where('type_livraison', $serviceType)
                     ->where('tranche_poids', $weightRange)
                     ->first();

        if (!$tarif) {
            $tarifsZone = self::where('zone', $zone->type_zone)->get();

            if ($tarifsZone->count() > 0) {
                $serviceTypeExists = $tarifsZone->where('type_livraison', $serviceType)->count() > 0;
                $weightRangeExists = $tarifsZone->where('tranche_poids', $weightRange)->count() > 0;

                \Log::info('Type de livraison: ' . $serviceType . ' existe: ' . ($serviceTypeExists ? 'Oui' : 'Non'));
                \Log::info('Tranche de poids: ' . $weightRange . ' existe: ' . ($weightRangeExists ? 'Oui' : 'Non'));

                if (!$serviceTypeExists || !$weightRangeExists) {
                    \Log::info('Tentative de correspondance partielle...');
                    // Sélectionner le premier tarif disponible pour cette zone comme fallback
                    return $tarifsZone->first();
                }
            }
        }

        return $tarif;
    }
}
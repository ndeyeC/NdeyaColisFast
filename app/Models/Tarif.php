<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = [
        'zone', 'type_livraison', 'tranche_distance', 
        'tranche_poids', 'prix',  'type_zone' 
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
             'extra_urbaine' => 'Extra-urbaine'
        ];
     }
   
     public static function findTarif($regionDepart, $regionArrivee, $serviceType, $weightRange)
{
   // 1. Trouver la zone correspondante
   $zone = Zone::where('region_depart', $regionDepart)
   ->where('region_arrivee', $regionArrivee)
   ->first();

if (!$zone) {
return null;
}

// Normaliser le type de service (standard/express) pour qu'il corresponde à la BDD
$serviceType = strtolower($serviceType);

// 2. Trouver le tarif en utilisant le type_zone de la Zone trouvée
$tarif = self::where('zone', $zone->type_zone)
    ->where('type_livraison', $serviceType)
    ->where('tranche_poids', $weightRange)
    ->first();

// Si aucun tarif n'est trouvé, on peut essayer une recherche plus large
if (!$tarif) {
// Rechercher par le type de zone seulement pour voir ce qui est disponible
$tarifsZone = self::where('zone', $zone->type_zone)->get();

// Si des tarifs existent pour cette zone, vérifier si c'est le type de livraison ou le poids qui pose problème
if ($tarifsZone->count() > 0) {
 // Vérifier si le type de livraison existe
 $serviceTypeExists = $tarifsZone->where('type_livraison', $serviceType)->count() > 0;
 
 // Vérifier si la tranche de poids existe
 $weightRangeExists = $tarifsZone->where('tranche_poids', $weightRange)->count() > 0;
 
 // Log des informations pour le débogage
 \Log::info('Type de livraison: ' . $serviceType . ' existe: ' . ($serviceTypeExists ? 'Oui' : 'Non'));
 \Log::info('Tranche de poids: ' . $weightRange . ' existe: ' . ($weightRangeExists ? 'Oui' : 'Non'));
 
 // Chercher par correspondance partielle si nécessaire
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Zone;


class ZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Liste des 14 régions du Sénégal
          $regions = [
            'Dakar', 'Thiès', 'Kaolack', 'Mbour', 'Louga', 'Ziguinchor', 'Saint-Louis',
            'Rufisque', 'Pikine', 'Guédiawaye', 'Matam', 'Tambacounda', 'Kolda', 'Fatick','Kedouguou','Sedhiou','Fatick','Thies','Diourbel','Bambilor'
        ];

        // Tableau des zones : définis selon les critères géographiques ou tes besoins métier
        $zoneMappings = [
            'intra_urbaine' => ['Dakar', 'Rufisque', 'Pikine', 'Guédiawaye', 'Thiès', 'Mbour'], // Par exemple, les villes proches
            'region_proche' => ['Kaolack', 'Mbour', 'Thiès', 'Louga', 'Fatick'], // Villes voisines mais pas urbaines
            'region_eloignee' => ['Ziguinchor', 'Saint-Louis', 'Tambacounda'], // Villes éloignées
            'extra_urbaine' => ['Ziguinchor', 'Saint-Louis', 'Matam', 'Kolda'], // Très éloignées
        ];

        // Créer toutes les combinaisons possibles entre toutes les régions
        // foreach ($regions as $regionDepart) {
        //     foreach ($regions as $regionArrivee) {
        //         // Ne pas créer de doublons si la région de départ est la même que la région d'arrivée
        //         if ($regionDepart !== $regionArrivee) {
        //             foreach ($zoneMappings as $typeZone => $zones) {
        //                 // Si les deux régions font partie du même groupe de zones
        //                 if (in_array($regionDepart, $zones) && in_array($regionArrivee, $zones)) {
        //                     // Vérifier si cette combinaison existe déjà dans la base de données
        //                     if (!Zone::where('region_depart', $regionDepart)
        //                         ->where('region_arrivee', $regionArrivee)
        //                         ->exists()) {
        //                         // Créer la nouvelle zone
        //                         Zone::create([
        //                             'region_depart' => $regionDepart,
        //                             'region_arrivee' => $regionArrivee,
        //                             'type_zone' => $typeZone
        //                         ]);
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        foreach ($regions as $regionDepart) {
    foreach ($regions as $regionArrivee) {
        foreach ($zoneMappings as $typeZone => $zones) {
            // Si les deux régions font partie du même groupe de zones
            if (in_array($regionDepart, $zones) && in_array($regionArrivee, $zones)) {
                // Vérifier si cette combinaison existe déjà dans la base de données
                if (!Zone::where('region_depart', $regionDepart)
                    ->where('region_arrivee', $regionArrivee)
                    ->exists()) {
                    // Créer la nouvelle zone (même si départ == arrivée)
                    Zone::create([
                        'region_depart' => $regionDepart,
                        'region_arrivee' => $regionArrivee,
                        'type_zone' => $typeZone
                    ]);
                }
            }
        }
    }
}

    }
}

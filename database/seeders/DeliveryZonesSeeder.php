<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeliveryZone;
use App\Models\DeliveryArea;



class DeliveryZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Dakar Centre',
                'description' => 'Zone centrale de Dakar',
                'base_token_price' => 2000,
                'areas' => [
                    'Plateau', 'Médina', 'Gueule Tapée', 'Fann', 'Point E', 'Amitié'
                ]
            ],
            [
                'name' => 'Dakar Périphérie',
                'description' => 'Banlieue de Dakar',
                'base_token_price' => 2500,
                'areas' => [
                    'Pikine', 'Guédiawaye', 'Parcelles Assainies', 'Grand Yoff', 'Ouakam', 'Yoff'
                ]
            ],
            [
                'name' => 'Zone Centre',
                'description' => 'Thiès, Mbour, Kaolack',
                'base_token_price' => 3000,
                'areas' => [
                    'Thiès', 'Mbour', 'Kaolack', 'Fatick', 'Diourbel'
                ]
            ],
            [
                'name' => 'Zone Nord',
                'description' => 'Saint-Louis, Louga',
                'base_token_price' => 3500,
                'areas' => [
                    'Saint-Louis', 'Louga', 'Richard Toll', 'Dagana', 'Podor'
                ]
            ],
            [
                'name' => 'Zone Sud',
                'description' => 'Ziguinchor, Kolda',
                'base_token_price' => 4000,
                'areas' => [
                    'Ziguinchor', 'Kolda', 'Sédhiou', 'Cap Skirring', 'Bignona'
                ]
            ],
            [
                'name' => 'Zone Est',
                'description' => 'Tambacounda, Kédougou',
                'base_token_price' => 4500,
                'areas' => [
                    'Tambacounda', 'Kédougou', 'Bakel', 'Goudiry', 'Vélingara'
                ]
            ],
        ];

        foreach ($zones as $zoneData) {
            $areas = $zoneData['areas'];
            unset($zoneData['areas']);
            
            // Créer la zone
            $zone = DeliveryZone::create($zoneData);
            
            // Créer les quartiers/villes associés
            foreach ($areas as $areaName) {
                DeliveryArea::create([
                    'name' => $areaName,
                    'delivery_zone_id' => $zone->id
                ]);
            }
        }
    }
    
}

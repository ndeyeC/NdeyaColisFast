<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZonesTableSeeder extends Seeder
{
    /**
     * Liste des régions du Sénégal (corrigée pour supprimer les doublons)
     */
    private $regions = [
        'Dakar', 'Thiès', 'Kaolack', 'Mbour', 'Louga', 'Ziguinchor', 'Saint-Louis',
        'Rufisque', 'Pikine', 'Guédiawaye', 'Matam', 'Tambacounda', 'Kolda', 'Fatick',
        'Kedouguou', 'Sedhiou', 'Diourbel', 'Bambilor'
    ];

    /**
     * Mappings des zones selon les critères géographiques
     */
    private $zoneMappings = [
        'intra_urbaine' => ['Dakar', 'Rufisque', 'Pikine', 'Guédiawaye', 'Thiès', 'Mbour'],
        'region_proche' => ['Kaolack', 'Louga', 'Fatick'],
        'region_eloignee' => ['Ziguinchor', 'Saint-Louis', 'Tambacounda', 'Kolda', 'Matam'],
    ];

    /**
     * Types de zones valides
     */
    private $validZoneTypes = [
        'intra_urbaine',
        'region_proche',
        'region_eloignee',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer toutes les données existantes dans la table zones
        Zone::query()->delete();

        // Tableau pour stocker les données à insérer
        $data = [];

        foreach ($this->regions as $regionDepart) {
            foreach ($this->regions as $regionArrivee) {
                // Inclure A → A pour les livraisons intra-régionales, mais éviter A → B et B → A
                if ($regionDepart > $regionArrivee) {
                    continue;
                }

                // Déterminer le type de zone
                $typeZone = $this->detectTypeZone($regionDepart, $regionArrivee);

                // Vérifier que le type de zone est valide
                if (!in_array($typeZone, $this->validZoneTypes)) {
                    $this->command->warn("Type de zone invalide pour {$regionDepart} → {$regionArrivee}: {$typeZone}");
                    continue;
                }

                $data[] = [
                    'region_depart' => $regionDepart,
                    'region_arrivee' => $regionArrivee,
                    'type_zone' => $typeZone,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insertion en masse
        if (!empty($data)) {
            Zone::insert($data);
            $this->command->info(' ' . count($data) . ' ');
        } else {
            $this->command->info('Aucune nouvelle zone à générer.');
        }
    }

    /**
     * Détermine le type de zone pour une paire de régions
     */
    private function detectTypeZone(string $regionDepart, string $regionArrivee): string
    {
        foreach ($this->zoneMappings as $typeZone => $zones) {
            if (in_array($regionDepart, $zones) && in_array($regionArrivee, $zones)) {
                return $typeZone;
            }
        }

        // Par défaut, si aucune correspondance
        return 'region_eloignee';
    }
}
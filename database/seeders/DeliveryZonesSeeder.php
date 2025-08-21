<?php  

namespace Database\Seeders;  

use Illuminate\Database\Seeder;  
use App\Models\DeliveryZone;  
use Illuminate\Support\Facades\DB;  

class DeliveryZonesSeeder extends Seeder  
{  
    public function run(): void  
    {  
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');  

        DeliveryZone::truncate();  

        $zones = [  
            [  
                'name' => 'Dakar',  
                'description' => 'Zone de livraison Dakar',  
                'base_token_price' => 1000,  
            ],  
        ];  

        foreach ($zones as $zoneData) {  
            DeliveryZone::create($zoneData);  
        }  

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');  
    }  
}

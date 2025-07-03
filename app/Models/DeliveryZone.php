<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryZone extends Model
{
    use HasFactory;
    
    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'base_token_price'];
    
    /**
     * Relations
     */
    public function areas()
    {
        return $this->hasMany(DeliveryArea::class);
    }
    
    /**
     * Récupérer toutes les zones avec leurs prix
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllZonesWithPrices()
    {
        return self::orderBy('name')->get();
    }
    
    /**
     * Relation avec les transactions de jetons
     */
    public function tokenTransactions()
    {
        return $this->hasMany(TokenTransaction::class, 'delivery_zone_id');
    }
    
    /**
     * Relation avec les livraisons
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'delivery_zone_id');
    }
}
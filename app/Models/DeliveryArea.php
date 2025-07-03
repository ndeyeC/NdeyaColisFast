<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryArea extends Model
{
    protected $fillable = ['name', 'delivery_zone_id'];
    
    // Relations
    public function zone()
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }
}

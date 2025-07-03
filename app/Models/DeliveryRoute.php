<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{
    protected $fillable = [
    'commnande_id', 'driver_id', 'start_point', 'end_point',
    'polyline', 'steps', 'distance_km', 'duration_minutes'
];

protected $casts = [
    'start_point' => 'array',
    'end_point' => 'array',
    'polyline' => 'array',
    'steps' => 'array'
];

public function commnande()
{
    return $this->belongsTo(Commnande::class, 'commnande_id');
}
}

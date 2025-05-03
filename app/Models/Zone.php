<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['region_depart', 'region_arrivee', 'type_zone'];

}

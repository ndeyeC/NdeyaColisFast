<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historique_livraison extends Model
{
    protected $fillable = ['Date_creation'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailLivraisons()
    {
        return $this->hasMany(DetailLivraison::class, 'id_historique');
    }
}

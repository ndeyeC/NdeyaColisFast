<?php

 namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class Commnande extends Model
 {
    protected $fillable = [
        'quantite',      
        'adresse_arrivee',  
        'adresse_depart',
        'type_livraison',
          'prix' ,
          'mode_paiement'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'id_commande');
    }

    public function detailLivraisons()
    {
        return $this->hasMany(DetailLivraison::class, 'id_commande');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLivraison extends Model
{
    protected $fillable = [
         
        'date_livraison',      
        'statut_livraison',    
        'prix_livraison',     
        'duree_livraison',
    ];
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande');
    }

    public function historique_livraison()
    {
        return $this->belongsTo(Historique_livraison::class, 'id_historique');
    }

    public function livreur()
    {
        return $this->belongsTo(Livreur::class, 'id_livreur');
    }
}

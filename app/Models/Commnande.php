<?php

 namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

 class Commnande extends Model
 {
    use SoftDeletes;

    const STATUT_EN_ATTENTE = 'en_attente_paiement';
    const STATUT_PAYEE = 'payee';
    const STATUT_CONFIRMEE = 'confirmee';
    const STATUT_ANNULEE = 'annulee';
    const STATUT_LIVREE = 'livree'; 
    const STATUT_ACCEPTEE = 'acceptee';
    const STATUT_EN_COURS = 'en_cours';


    
    // Liste des statuts considérés comme disponibles pour les livreurs
    public static function statutsDisponibles()
    {
        return [
            self::STATUT_EN_ATTENTE,
            self::STATUT_PAYEE, 
            self::STATUT_CONFIRMEE
        ];
    }

    
public static function statutsAcceptables(): array
{
    return [
         self::STATUT_EN_ATTENTE,
        self::STATUT_PAYEE,
        self::STATUT_CONFIRMEE,
    ];
}
public function getProblemeDecodeAttribute()
{
    return $this->probleme_signale ?? [];
}

public static function statutsEnCours(): array
{
    return [
        self::STATUT_ACCEPTEE,
        self::STATUT_EN_COURS,
    ];
}

public static function statutsDuLivreur(): array
{
    return [
        self::STATUT_ACCEPTEE,
        self::STATUT_EN_COURS,
        self::STATUT_LIVREE,
    ];
}
    
 protected $casts = [
     'probleme_signale' => 'array' // Conversion automatique
 ];

    protected $fillable = [
        'quantite',      
        'adresse_arrivee',  
        'adresse_depart',
        'type_livraison',
          'prix' ,
          'mode_paiement',
          'reference',    
          'type_colis',     
          'prix',
        'payment_token',
          'payment_token',
          'prix_base',     
          'prix_final',
          'user_id',
          'region_depart',   
         'region_arrivee',  
        'type_zone' ,
        'status',
        'details_adresse_arrivee',
        'details_adresse_depart',
        'lat_depart',
        'lat_arrivee',
        'commentaire_livraison',
        'lng_depart',
        'lng_arrivee',
        'driver_id' ,
        'temps_livraison',
        'livraison_complete',
        'probleme_signale',
        'numero_telephone',
        'raison_annulation',
        'date_annulation',
        'photo_livraison',
        'temps_livraison',
         'trajet_id'





    ];
    public function user()
    {
    return $this->belongsTo(User::class, 'user_id', 'user_id');

    }
    

    public function driver()
{
    return $this->belongsTo(User::class, 'driver_id', 'user_id');

}
public function deliveryRoute()
{
    return $this->hasOne(DeliveryRoute::class, 'commande_id');
}


    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'id_commande');
    }

    public function detailLivraisons()
    {
        return $this->hasMany(DetailLivraison::class, 'id_commande');
    }

     public function evaluation()
    {
        return $this->hasOne(Evaluation::class, 'commande_id', 'id');
    }

   
    public function trajet()
{
    return $this->belongsTo(TrajetUrbain::class, 'trajet_id');
}


    // Scopes
    public function scopeLivreesParPeriode($query, $debut, $fin)
    {
        return $query->where('status', self::STATUT_LIVREE)
                    ->whereBetween('updated_at', [$debut, $fin]);
    }

    public function scopeParQuartier($query, $quartier = null)
    {
        if ($quartier) {
            return $query->where('region_arrivee', 'like', "%{$quartier}%");
        }
        return $query;
    }

    

    // Méthodes utilitaires
    public function isLivree(): bool
    {
        return $this->status === self::STATUT_LIVREE;
    }

    public function isEnCours(): bool
    {
        return in_array($this->status, self::statutsEnCours());
    }

    public function isAcceptable(): bool
    {
        return in_array($this->status, self::statutsAcceptables());
    }
}



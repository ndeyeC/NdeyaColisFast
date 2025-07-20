<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Commnande;



class TrajetUrbain extends Model
{
    use HasFactory;

    
    // Force Laravel à utiliser trajets_urbains
    protected $table = 'trajets_urbains';

    protected $fillable = [
        'livreur_id',
        'type_voiture',
        'matricule',
        'heure_depart',
        'destination_region'
    ];

    public function livreur()
{
    return $this->belongsTo(User::class, 'livreur_id');
}


public function detailLivraisons()
    {
        return $this->hasMany(DetailLivraison::class, 'trajet_id');
    }

    public function commnandes()
{
    return $this->hasMany(Commnande::class, 'trajet_id');
}

}

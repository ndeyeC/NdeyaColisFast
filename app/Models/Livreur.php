<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Livreur extends User
{
    protected $fillable = ['Vehicule', 'statut'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['role'] = 'livreur';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailLivraisons()
    {
        return $this->hasMany(DetailLivraison::class, 'id_livreur');
    }

    public function sentCommunications()
{
    return $this->morphMany(Communication::class, 'sender');
}

public function receivedCommunications()
{
    return $this->morphMany(Communication::class, 'receiver');
}

public function communications()
{
    return $this->sentCommunications()->union($this->receivedCommunications());
}
}

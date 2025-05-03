<?php

 namespace App\Models;

 use Illuminate\Database\Eloquent\Model;

class Client extends User
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['role'] = 'client';
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('role', function ($query) {
            $query->where('role', 'client');
        });
    }
    public function commandes()
     {
         return $this->hasMany(Commande::class, 'id_utilisateur', 'id_utilisateur');
     }
 }

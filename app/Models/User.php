<?php

 namespace App\Models;

  use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Foundation\Auth\User as Authenticatable;
 use Illuminate\Notifications\Notifiable;

 class User extends Authenticatable
 {
    /** @use HasFactory<\Database\Factories\UserFactory> */
     use HasFactory, Notifiable;

     /**
     * The attributes that are mass assignable.
      *
      * @var list<string>
      */
 use Notifiable;

 protected $table = 'users';
protected $primaryKey = 'user_id';
protected $fillable = [
        'name',
         'email',
        'password',
        'numero_telephone',
        'adress',
        'role',
     ];
     

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'user_id');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'user_id');
    }

    public function historiqueLivraisons()
    {
        return $this->hasMany(HistoriqueLivraison::class, 'user_id');
    }

    public function tokens() {
        return $this->hasMany(TokenTransaction::class, 'user_id', 'user_id');
    }
    
    
    public function getTokenBalanceAttribute() {
        return $this->tokens()->sum('amount');
    }

    public function livreur()
    {
        return $this->hasOne(Livreur::class, 'user_id');
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

     /**
      * The attributes that should be hidden for serialization.
      *
      * @var list<string>
      */
    protected $hidden = [
         'password',
         'remember_token',
     ];

    /**
     * Get the attributes that should be cast.
      *
     * @return array<string, string>
     */
    protected function casts(): array
     {
         return [
             'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

   
 }

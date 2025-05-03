<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $fillable = ['user_id', 'livraison_id', 'message', 'is_admin','sender_id', 
    'receiver_id','is_read','receiver_type','sender_type'

];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livreur()
    {
        return $this->belongsTo(Livreur::class,'livraison_id');
    }

    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    public function livraison()
    {
        return $this->belongsTo(Livraison::class);
    }
}

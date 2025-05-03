<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message_id', 'content'];

    // Relation avec le message auquel la réponse appartient
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    // Relation avec l'utilisateur qui a répondu
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

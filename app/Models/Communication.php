<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $fillable = [
        'sender_id', 
        'sender_type',
        'receiver_id',
        'receiver_type',
        'message',
        'is_read'
    ];

    // Relations polymorphiques
    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    // Relations spécifiques (optionnelles pour la compatibilité)
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id')->where('sender_type', 'App\Models\User');
    }

    public function livreur()
    {
        return $this->belongsTo(Livreur::class, 'sender_id')->where('sender_type', 'App\Models\Livreur');
    }

    /**
     * Récupère toutes les conversations entre un utilisateur et l'admin
     * 
     * @param int $userId
     * @param string $userType
     * @param int $adminId
     * @return \Illuminate\Database\Eloquent\Builder
     */
   public static function userConversations($userId, $userType = 'App\Models\User', $adminId = null)
{
    $adminId = $adminId ?? getAdminId();

    return self::where(function($query) use ($userId, $userType, $adminId) {
        $query->where('sender_id', $userId)
              ->where('sender_type', $userType)
              ->where('receiver_id', $adminId)
              ->where('receiver_type', 'App\Models\User');
    })->orWhere(function($query) use ($userId, $userType, $adminId) {
        $query->where('receiver_id', $userId)
              ->where('receiver_type', $userType)
              ->where('sender_id', $adminId)
              ->where('sender_type', 'App\Models\User');
    })->orderBy('created_at', 'asc');
}


    /**
     * Récupère les nouveaux messages pour un utilisateur depuis un ID donné
     * 
     * @param int $userId
     * @param string $userType
     * @param int $lastId
     * @param int $adminId
     * @return \Illuminate\Database\Eloquent\Collection
     */
   public static function newMessagesForUser($userId, $userType, $lastId, $adminId = null)
{
    $adminId = $adminId ?? getAdminId();

    return self::where(function($query) use ($userId, $userType, $adminId) {
        $query->where('sender_id', $userId)
              ->where('sender_type', $userType)
              ->where('receiver_id', $adminId)
              ->where('receiver_type', 'App\Models\User');
    })->orWhere(function($query) use ($userId, $userType, $adminId) {
        $query->where('receiver_id', $userId)
              ->where('receiver_type', $userType)
              ->where('sender_id', $adminId)
              ->where('sender_type', 'App\Models\User');
    })
    ->where('id', '>', $lastId)
    ->orderBy('created_at', 'asc')
    ->get();
}


    /**
     * Marque tous les messages reçus par un utilisateur comme lus
     * 
     * @param int $userId
     * @param string $userType
     * @return int
     */
    public static function markAsRead($userId, $userType)
    {
        return self::where('receiver_id', $userId)
                  ->where('receiver_type', $userType)
                  ->where('is_read', false)
                  ->update(['is_read' => true]);
    }

    /**
     * Compte les messages non lus pour un utilisateur
     * 
     * @param int $userId
     * @param string $userType
     * @return int
     */
    public static function unreadCount($userId, $userType)
    {
        return self::where('receiver_id', $userId)
                  ->where('receiver_type', $userType)
                  ->where('is_read', false)
                  ->count();
    }
}
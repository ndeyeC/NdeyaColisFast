<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'is_read'];
    
    public $timestamps = true;
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
        return $this;
    }

    // Scope pour les notifications non lues
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
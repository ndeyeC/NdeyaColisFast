<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TokenTransaction extends Model 
{
    protected $fillable = [
        'user_id', 
        'amount', 
        'payment_method', 
        'delivery_zone_id',
        'status', 
        'reference',
        'notes',
        'expiry_date'
    ];
    
    protected $dates = ['created_at', 'updated_at', 'expiry_date'];
    
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    
    public function user() 
    {
        return $this->belongsTo(User::class);
    }
    
    public function zone()
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }
    
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
    
    public function daysUntilExpiry()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

     protected $casts = [
        'expiry_date' => 'datetime',
    ];
    
}
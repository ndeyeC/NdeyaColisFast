<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        'expiry_date',
        'type',
        'commande_id',
    ];
    
    protected $dates = ['created_at', 'updated_at', 'expiry_date'];
    
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    
    // Type constants
    const TYPE_PURCHASE = 'achat'; // CORRECTION: Correspondre à votre DB
    const TYPE_USAGE = 'usage';
    const TYPE_REFUND = 'refund';
    const TYPE_EXPIRY = 'expiry';
    
    // Payment method constants
    const PAYMENT_CINETPAY = 'cinetpay';
    const PAYMENT_MOBILE_MONEY = 'mobile_money';
    const PAYMENT_BANK_CARD = 'bank_card';
    
    protected $casts = [
        'expiry_date' => 'datetime',
        'amount' => 'integer',
    ];
    
    // Relations
    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id'); // CORRECTION: Spécifier les clés
    }

    public function commande()
    {
        return $this->belongsTo(Commnande::class, 'commande_id');
    }
    
    public function zone()
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }
    
    // Méthodes utilitaires
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
    
    public function isPurchase()
    {
        return $this->type === self::TYPE_PURCHASE && $this->amount > 0;
    }
    
    public function isUsage()
    {
        return $this->type === self::TYPE_USAGE && $this->amount < 0;
    }
    
    public function isRefund()
    {
        return $this->type === self::TYPE_REFUND && $this->amount > 0;
    }
    
    public function isDakarZone()
    {
        if (!$this->zone) {
            return false;
        }
        
        // CORRECTION: Une seule zone Dakar
        return $this->zone->name === 'Dakar';
    }
    
    // CORRECTION: Scope pour les transactions valides
    public function scopeValid($query)
    {
        return $query->where('status', self::STATUS_COMPLETED)
                     ->where('type', self::TYPE_PURCHASE)
                     ->where(function($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>', Carbon::now());
                     });
    }
    
    public function scopePurchases($query)
    {
        return $query->where('type', self::TYPE_PURCHASE)->where('amount', '>', 0);
    }
    
    public function scopeUsages($query)
    {
        return $query->where('type', self::TYPE_USAGE)->where('amount', '<', 0);
    }

    // CORRECTION: Scope pour la zone Dakar
    public function scopeDakarZone($query)
    {
        // Récupérer l'ID de la zone Dakar dynamiquement
        $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
        $dakarZoneId = $dakarZone ? $dakarZone->id : null;
        
        return $query->where(function($q) use ($dakarZoneId) {
            if ($dakarZoneId) {
                $q->where('delivery_zone_id', $dakarZoneId)
                  ->orWhereNull('delivery_zone_id'); // Inclure les jetons sans zone spécifique
            } else {
                $q->whereNull('delivery_zone_id');
            }
        });
    }
    
    public function scopeForZone($query, $zoneId)
    {
        return $query->where('delivery_zone_id', $zoneId);
    }
    
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    // CORRECTION: Méthodes statiques pour créer les transactions
    public static function createPurchase($userId, $amount, $zoneId, $paymentMethod, $reference = null, $expiryDays = 365)
    {
        // CORRECTION: Validation du user_id
        if (!$userId) {
            throw new \Exception('User ID manquant pour createPurchase');
        }

        return self::create([
            'user_id' => $userId,
            'amount' => abs($amount), // S'assurer que c'est positif
            'delivery_zone_id' => $zoneId,
            'payment_method' => $paymentMethod,
            'type' => self::TYPE_PURCHASE,
            'status' => self::STATUS_PENDING, // CORRECTION: Commencer par pending
            'reference' => $reference ?: 'TOKEN-' . strtoupper(\Str::random(8)),
            'notes' => "Achat de {$amount} jeton(s) pour la zone",
            'expiry_date' => Carbon::now()->addDays($expiryDays),
        ]);
    }
    
    public static function createUsage($userId, $amount, $zoneId, $commandeId, $reference = null)
    {
        // CORRECTION: Validation du user_id
        if (!$userId) {
            throw new \Exception('User ID manquant pour createUsage');
        }

        return self::create([
            'user_id' => $userId,
            'amount' => -abs($amount), // S'assurer que c'est négatif
            'delivery_zone_id' => $zoneId,
            'commande_id' => $commandeId,
            'type' => self::TYPE_USAGE,
            'status' => self::STATUS_COMPLETED,
            'reference' => $reference,
            'notes' => "Utilisation de {$amount} jeton(s) pour commande {$reference}",
            'payment_method' => 'token',
        ]);
    }

    public static function createRefund($userId, $amount, $zoneId, $commandeId, $reason = null)
    {
        // CORRECTION: Validation du user_id
        if (!$userId) {
            throw new \Exception('User ID manquant pour createRefund');
        }

        return self::create([
            'user_id' => $userId,
            'amount' => abs($amount), // S'assurer que c'est positif
            'delivery_zone_id' => $zoneId,
            'commande_id' => $commandeId,
            'type' => self::TYPE_REFUND,
            'status' => self::STATUS_COMPLETED,
            'reference' => 'REFUND-' . strtoupper(\Str::random(8)),
            'notes' => "Remboursement: " . ($reason ?: "Commande annulée"),
            'payment_method' => 'token',
        ]);
    }

    // CORRECTION: Méthode pour marquer comme complété
    public function markAsCompleted()
    {
        if ($this->status === self::STATUS_PENDING) {
            $this->update(['status' => self::STATUS_COMPLETED]);
            
            // CORRECTION: Notifier l'utilisateur que ses jetons ont été mis à jour
            if ($this->user) {
                $this->user->refresh();
                
                Log::info('Transaction marquée comme complétée', [
                    'transaction_id' => $this->id,
                    'user_id' => $this->user_id,
                    'type' => $this->type,
                    'amount' => $this->amount,
                    'new_balance' => $this->user->token_balance
                ]);
            }
        }
        
        return $this;
    }
    
    // Formatage pour l'affichage
    public function getFormattedAmountAttribute()
    {
        $prefix = $this->amount > 0 ? '+' : '';
        $suffix = abs($this->amount) > 1 ? ' jetons' : ' jeton';
        
        return $prefix . $this->amount . $suffix;
    }
    
    public function getTypeDisplayAttribute()
    {
        switch ($this->type) {
            case self::TYPE_PURCHASE:
                return 'Achat';
            case self::TYPE_USAGE:
                return 'Utilisation';
            case self::TYPE_REFUND:
                return 'Remboursement';
            case self::TYPE_EXPIRY:
                return 'Expiration';
            default:
                return 'Inconnu';
        }
    }
    
    public function getStatusDisplayAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'En attente';
            case self::STATUS_COMPLETED:
                return 'Terminé';
            case self::STATUS_FAILED:
                return 'Échoué';
            default:
                return 'Inconnu';
        }
    }
    
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'text-yellow-600';
            case self::STATUS_COMPLETED:
                return 'text-green-600';
            case self::STATUS_FAILED:
                return 'text-red-600';
            default:
                return 'text-gray-600';
        }
    }

    // CORRECTION: Boot method pour observer les changements
    protected static function boot()
    {
        parent::boot();
        
        // Observer pour les créations/mises à jour
        static::created(function ($transaction) {
            Log::info('Nouvelle transaction créée', [
                'id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'status' => $transaction->status
            ]);
        });
        
        static::updated(function ($transaction) {
            if ($transaction->wasChanged('status')) {
                Log::info('Statut transaction changé', [
                    'id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'old_status' => $transaction->getOriginal('status'),
                    'new_status' => $transaction->status
                ]);
                
                // Rafraîchir l'utilisateur si la transaction est complétée
                if ($transaction->status === self::STATUS_COMPLETED && $transaction->user) {
                    $transaction->user->refresh();
                }
            }
        });
    }
}
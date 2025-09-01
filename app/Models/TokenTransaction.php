<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TokenTransaction extends Model
{
    protected $table = 'token_transactions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const TYPE_ACHAT = 'achat';
    const TYPE_USAGE = 'usage';
    const TYPE_REFUND = 'refund';

    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'delivery_zone_id',
        'status',
        'reference',
        'expiry_date',
        'type',
        'commande_id',
        'notes',
        'paydunya_token',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    // ================= RELATIONS =================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function zone()
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function commande()
    {
        return $this->belongsTo(Commnande::class, 'commande_id');
    }

    // ================= MÉTHODES STATUT =================

    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
        Log::info('Transaction marquée comme complétée', ['transaction_id' => $this->id]);
    }

    public function markAsFailed($reason)
    {
        $this->status = self::STATUS_FAILED;
        $this->notes = $reason;
        $this->save();
        Log::info('Transaction marquée comme échouée', ['transaction_id' => $this->id, 'reason' => $reason]);
    }

    public function setPayDunyaToken($token)
    {
        $this->paydunya_token = $token;
        $this->save();
    }

    // ================= MÉTHODES EXPIRATION =================

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        $now = Carbon::now();
        $expiry = Carbon::parse($this->expiry_date);

        return max(0, $now->diffInDays($expiry, false));
    }

    // ================= MÉTHODES DE TRANSACTION =================

    public static function createUsage($userId, $amount, $zoneId, $commandeId = null, $reference = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_USAGE,
            'status' => self::STATUS_COMPLETED,
            'amount' => -abs($amount),
            'delivery_zone_id' => $zoneId,
            'commande_id' => $commandeId,
            'reference' => $reference ?? 'USAGE-' . uniqid(),
            'notes' => 'Utilisation de jetons pour livraison',
            'payment_method' => 'token',
        ]);
    }

    public static function createRefund($userId, $amount, $zoneId, $commandeId = null, $reason = 'Remboursement')
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_REFUND,
            'status' => self::STATUS_COMPLETED,
            'amount' => abs($amount),
            'delivery_zone_id' => $zoneId,
            'commande_id' => $commandeId,
            'notes' => $reason,
            'payment_method' => 'token',
        ]);
    }

    // ================= MÉTHODES DE SOLDE =================

    /**
     * Retourne le solde de jetons valides pour une zone spécifique (FIFO)
     */
    public function getValidTokensForZone($zoneId)
    {
        $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
        $dakarZoneId = $dakarZone ? $dakarZone->id : null;

        $purchases = $this->user->tokenTransactions()
            ->where('status', self::STATUS_COMPLETED)
            ->where('type', self::TYPE_ACHAT)
            ->where(function ($query) use ($zoneId, $dakarZoneId) {
                $query->where('delivery_zone_id', $zoneId)
                      ->orWhere('delivery_zone_id', $dakarZoneId)
                      ->orWhereNull('delivery_zone_id');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $totalUsages = $this->user->tokenTransactions()
            ->where('status', self::STATUS_COMPLETED)
            ->where('type', self::TYPE_USAGE)
            ->where(function ($query) use ($zoneId, $dakarZoneId) {
                $query->where('delivery_zone_id', $zoneId)
                      ->orWhere('delivery_zone_id', $dakarZoneId)
                      ->orWhereNull('delivery_zone_id');
            })
            ->sum('amount');

        $available = 0;
        $remainingUsages = abs($totalUsages);

        foreach ($purchases as $purchase) {
            $purchaseAmount = $purchase->amount;
            $effectiveAmount = max(0, $purchaseAmount - $remainingUsages);
            $remainingUsages = max(0, $remainingUsages - $purchaseAmount);

            if (!$purchase->expiry_date || $purchase->expiry_date > now()) {
                $available += $effectiveAmount;
            }
        }

        return max(0, $available);
    }

    /**
     * Vérifie si l'utilisateur peut utiliser des jetons pour une zone
     */
    public function canUseTokensForZone($zoneId, $amount = 1)
    {
        return $this->getValidTokensForZone($zoneId) >= $amount;
    }

    /**
     * Rembourse des jetons pour une commande annulée
     */
    public function refundTokensForCancelledOrder($commandeId, $reason = 'Commande annulée')
    {
        $original = $this->user->tokenTransactions()
            ->where('commande_id', $commandeId)
            ->where('type', self::TYPE_USAGE)
            ->first();

        if (!$original) {
            throw new \Exception('Transaction de jetons introuvable pour cette commande.');
        }

        return self::createRefund(
            $this->user_id,
            abs($original->amount),
            $original->delivery_zone_id,
            $commandeId,
            $reason
        );
    }
}

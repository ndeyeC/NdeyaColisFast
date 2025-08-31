<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\SoftDeletes;



class User extends Authenticatable
{

        use SoftDeletes;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'password',
        'numero_telephone',
        'adress',
        'role',
        'vehicule',
        'id_card',
        'fcm_token',
        'type_livreur',
    ];

    // RELATIONSHIPS
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'user_id', 'user_id');
    }

    public function tokenTransactions()
    {
        return $this->hasMany(TokenTransaction::class, 'user_id', 'user_id');
    }

    public function commandes()
    {
        return $this->hasMany(Commnande::class, 'user_id', 'user_id');
    }

    public function historiqueLivraisons()
    {
        return $this->hasMany(HistoriqueLivraison::class, 'user_id', 'user_id');
    }

    public function tokens()
    {
        return $this->hasMany(TokenTransaction::class, 'user_id', 'user_id');
    }

   public function getTokenBalanceAttribute()
{
    // Utilisez le calcul FIFO pour la zone Dakar par défaut
    $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
    return $dakarZone ? $this->getValidTokensForZone($dakarZone->id) : 0;
}

    public function livreur()
    {
        return $this->hasOne(Livreur::class, 'user_id', 'user_id');
    }

    public function livreursFavoris()
{
    return $this->belongsToMany(User::class, 'favoris', 'user_id', 'livreur_id');
}

public function clientsFavoris()
{
    return $this->belongsToMany(User::class, 'favoris', 'livreur_id', 'user_id');
}

    public function sentCommunications()
    {
        return $this->morphMany(Communication::class, 'sender');
    }

    public function livraisons()
    {
        return $this->hasMany(Commnande::class, 'driver_id', 'user_id');
    }

    public function receivedCommunications()
    {
        return $this->morphMany(Communication::class, 'receiver');
    }

    public function communications()
    {
        return $this->sentCommunications()->union($this->receivedCommunications());
    }


     public function setIdCardAttribute($value)
    {
        $this->attributes['id_card'] = $value ? Crypt::encryptString($value) : null;
    }


     public function getIdCardAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // TOKEN METHODS - FIXED VERSIONS

    /**
     * Débiter les jetons pour une livraison (version corrigée)
     */
    public function debitTokensForDelivery($zoneId, $amount, $reference, $commandeId = null)
    {
        // FIXED: Use the correct primary key
        if (!$this->user_id) {
            \Log::error('Utilisateur non authentifié dans debitTokensForDelivery');
            throw new \Exception('Utilisateur non authentifié.');
        }

        // Vérification de sécurité : s'assurer que c'est la zone Dakar
        $zone = DeliveryZone::find($zoneId);
        if (!$zone) {
            throw new \Exception('Zone de livraison introuvable.');
        }

        \Log::info('Debug zone information', [
            'zone_id' => $zoneId,
            'zone_data' => $zone->toArray()
        ]);

        // FIXED: Vérification simplifiée pour une seule zone Dakar
        if ($zone->name !== 'Dakar') {
            \Log::error('Zone non-Dakar détectée', [
                'zone_id' => $zoneId,
                'zone_name' => $zone->name ?? 'N/A',
                'expected_zone' => 'Dakar'
            ]);
            throw new \Exception('Les jetons ne peuvent être utilisés que pour les livraisons dans Dakar.');
        }

        // Vérifier les jetons disponibles
        $validTokens = $this->getValidTokensForZone($zoneId);
        if ($validTokens < $amount) {
            throw new \Exception("Jetons insuffisants. Disponibles: {$validTokens}, Requis: {$amount}");
        }

        try {
            if (method_exists(TokenTransaction::class, 'createUsage')) {
                $transaction = TokenTransaction::createUsage(
                    $this->user_id,
                    $amount,
                    $zoneId,
                    $commandeId,
                    $reference
                );
            } else {
                // Fallback: créer manuellement
                $transaction = TokenTransaction::create([
                    'user_id' => $this->user_id,
                    'type' => 'usage',
                    'status' => 'completed',
                    'amount' => -abs($amount), // Montant négatif pour usage
                    'delivery_zone_id' => $zoneId,
                    'commande_id' => $commandeId,
                    'reference' => $reference,
                    'notes' => 'Utilisation de jetons pour livraison',
                    'payment_method' => 'token',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la transaction usage', [
                'error' => $e->getMessage(),
                'user_id' => $this->user_id,
                'amount' => $amount
            ]);
            throw new \Exception('Erreur lors du débitage des jetons: ' . $e->getMessage());
        }

        \Log::info('Jetons débités avec succès', [
            'user_id' => $this->user_id,
            'zone_id' => $zoneId,
            'amount' => $amount,
            'transaction_id' => $transaction->id,
            'reference' => $reference,
            'zone_name' => $zone->name ?? 'N/A'
        ]);

        return $transaction;
    }

    /**
     * Obtenir le solde de jetons valides pour une zone spécifique (FIXED)
     */
public function getValidTokensForZone($zoneId)
{
    if (!$this->user_id) {
        \Log::error('Aucun utilisateur authentifié dans getValidTokensForZone');
        throw new \Exception('Utilisateur non authentifié.');
    }

    $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
    $dakarZoneId = $dakarZone ? $dakarZone->id : null;

    // Récupérer tous les achats complétés, triés par date croissante (FIFO)
    $purchases = $this->tokenTransactions()
        ->where('status', 'completed')
        ->where('type', 'achat')
        ->where(function ($query) use ($zoneId, $dakarZoneId) {
            $query->where('delivery_zone_id', $zoneId)
                  ->orWhere('delivery_zone_id', $dakarZoneId)
                  ->orWhereNull('delivery_zone_id');
        })
        ->orderBy('created_at', 'asc') // Les plus anciens d'abord
        ->get();

    // Nombre total d'utilisations pour la zone
    $totalUsages = $this->tokenTransactions()
        ->where('status', 'completed')
        ->where('type', 'usage')
        ->where(function ($query) use ($zoneId, $dakarZoneId) {
            $query->where('delivery_zone_id', $zoneId)
                  ->orWhere('delivery_zone_id', $dakarZoneId)
                  ->orWhereNull('delivery_zone_id');
        })
        ->count();

    $available = 0;
    $remainingUsages = $totalUsages;

    foreach ($purchases as $purchase) {
        $purchaseAmount = $purchase->amount; // Typiquement 1 par achat, mais général

        // Attribuer les usages au jeton actuel
        if ($remainingUsages > 0) {
            $consumed = min($purchaseAmount, $remainingUsages);
            $remainingUsages -= $consumed;
            $effectiveAmount = $purchaseAmount - $consumed;
        } else {
            $effectiveAmount = $purchaseAmount;
        }

        // Ajouter seulement si non expiré
        if (!$purchase->expiry_date || $purchase->expiry_date > now()) {
            $available += max(0, $effectiveAmount);
        }
    }

    \Log::info('Calcul des jetons FIFO', [
        'user_id' => $this->user_id,
        'zone_id' => $zoneId,
        'dakar_zone_id' => $dakarZoneId,
        'total_purchases' => $purchases->count(),
        'total_usages' => $totalUsages,
        'available' => $available,
    ]);

    return max(0, $available);
}

    /**
     * Obtenir tous les jetons valides pour la zone Dakar - FIXED
     */
    public function getValidDakarTokens()
    {
        if (!$this->user_id) {
            \Log::error('Utilisateur non authentifié dans getValidDakarTokens');
            return collect();
        }

        // Récupérer l'ID de la zone Dakar dynamiquement
        $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
        if (!$dakarZone) {
            \Log::warning('Zone Dakar introuvable');
            return collect();
        }
        
        // Get all valid transactions for Dakar zone
        $tokens = $this->tokenTransactions()
            ->where('status', TokenTransaction::STATUS_COMPLETED)
            ->where(function ($query) use ($dakarZone) {
                $query->where('delivery_zone_id', $dakarZone->id)
                      ->orWhereNull('delivery_zone_id');
            })
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
            })
            ->sum('amount');

        Log::info('Jetons valides pour Dakar', [
            'user_id' => $this->user_id,
            'dakar_zone_id' => $dakarZone->id,
            'tokens' => $tokens,
        ]);

        // Ensure no negative balance
        return max(0, $tokens);
    }

    /**
     * Obtenir l'historique des jetons pour l'utilisateur - FIXED
     */
    public function getTokenHistory($limit = 10)
    {
        if (!$this->user_id) {
            return collect();
        }

        return $this->tokenTransactions()
            ->with(['zone', 'commande'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir le solde total de jetons valides pour Dakar - FIXED
     */
    public function getTotalValidDakarTokens()
    {
        if (!$this->user_id) {
            return 0;
        }

        // Récupérer l'ID de la zone Dakar dynamiquement
        $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
        if (!$dakarZone) {
            return 0;
        }
        
        return $this->tokenTransactions()
            ->where('status', TokenTransaction::STATUS_COMPLETED)
            ->where(function ($query) use ($dakarZone) {
                $query->where('delivery_zone_id', $dakarZone->id)
                      ->orWhereNull('delivery_zone_id');
            })
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
            })
            ->sum('amount');
    }

    /**
     * Vérifier si l'utilisateur peut utiliser des jetons pour une zone - FIXED
     */
    public function canUseTokensForZone($zoneId, $amount = 1)
    {
        if (!$this->user_id) {
            return false;
        }

        // Vérifier que c'est la zone Dakar
        $zone = DeliveryZone::find($zoneId);
        if (!$zone || $zone->name !== 'Dakar') {
            return false;
        }

        // Vérifier le solde
        return $this->getValidTokensForZone($zoneId) >= $amount;
    }

    /**
     * Rembourser des jetons (en cas d'annulation de commande) - FIXED
     */
    public function refundTokensForCancelledOrder($commandeId, $reason = 'Commande annulée')
    {
        if (!$this->user_id) {
            throw new \Exception('Utilisateur non authentifié.');
        }

        // Trouver la transaction de débit originale
        $originalTransaction = $this->tokenTransactions()
            ->where('commande_id', $commandeId)
            ->where('type', TokenTransaction::TYPE_USAGE)
            ->first();

        if (!$originalTransaction) {
            throw new \Exception('Transaction de jetons introuvable pour cette commande.');
        }

        // Créer la transaction de remboursement
        $refundTransaction = TokenTransaction::createRefund(
            $this->user_id,
            abs($originalTransaction->amount), // Montant positif
            $originalTransaction->delivery_zone_id,
            $commandeId,
            $reason
        );

        \Log::info('Jetons remboursés', [
            'user_id' => $this->user_id,
            'commande_id' => $commandeId,
            'amount_refunded' => abs($originalTransaction->amount),
            'refund_transaction_id' => $refundTransaction->id
        ]);

        return $refundTransaction;
    }

    // OTHER METHODS
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLivreur()
    {
        return $this->role === 'livreur';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function evaluationsClients()
    {
        return $this->hasMany(Evaluation::class, 'driver_id', 'user_id')->where('type_evaluation', 'client');
    }
}
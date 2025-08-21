<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
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
        return $this->hasMany(Commande::class, 'user_id', 'user_id');
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
        return $this->tokens()->sum('amount');
    }

    public function livreur()
    {
        return $this->hasOne(Livreur::class, 'user_id', 'user_id');
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

        // FIXED: Créer la transaction de débit manuellement si createUsage n'existe pas
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
        // FIXED: Use user_id consistently
        if (!$this->user_id) {
            \Log::error('Aucun utilisateur authentifié dans getValidTokensForZone');
            throw new \Exception('Utilisateur non authentifié.');
        }

        \Log::info('Appel de getValidTokensForZone', [
            'user_id' => $this->user_id,
            'zone_id' => $zoneId
        ]);

        // Debug: Vérifier toutes les transactions de l'utilisateur
        $allTransactions = $this->tokenTransactions()->get();
        \Log::info('Toutes les transactions de l\'utilisateur', [
            'user_id' => $this->user_id,
            'total_transactions' => $allTransactions->count(),
            'transactions' => $allTransactions->map(function($t) {
                return [
                    'id' => $t->id,
                    'amount' => $t->amount,
                    'type' => $t->type,
                    'status' => $t->status,
                    'zone_id' => $t->delivery_zone_id
                ];
            })->toArray()
        ]);

        // FIXED: Récupérer l'ID de la zone Dakar dynamiquement
        $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
        $dakarZoneId = $dakarZone ? $dakarZone->id : null;
        
        // FIXED: Use string values that match your database
        $purchaseQuery = $this->tokenTransactions()
            ->where('status', 'completed') // Use string directly
            ->where('type', 'achat')       // Use string directly (from your DB)
            ->where(function ($query) use ($zoneId, $dakarZoneId) {
                if ($dakarZoneId) {
                    $query->where('delivery_zone_id', $dakarZoneId)
                          ->orWhere('delivery_zone_id', $zoneId)
                          ->orWhereNull('delivery_zone_id');
                } else {
                    $query->whereNull('delivery_zone_id');
                }
            })
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
            });

        $purchaseTokens = $purchaseQuery->sum('amount');
        
        // Debug the purchase query
        \Log::info('Purchase query debug', [
            'sql' => $purchaseQuery->toSql(),
            'bindings' => $purchaseQuery->getBindings(),
            'results' => $purchaseQuery->get()->toArray()
        ]);
        
        // Get usage tokens (negative amounts)
        $usageQuery = $this->tokenTransactions()
            ->where('status', 'completed') // Use string directly
            ->where('type', 'usage')       // Use string directly
            ->where(function ($query) use ($zoneId, $dakarZoneId) {
                if ($dakarZoneId) {
                    $query->where('delivery_zone_id', $dakarZoneId)
                          ->orWhere('delivery_zone_id', $zoneId)
                          ->orWhereNull('delivery_zone_id');
                } else {
                    $query->whereNull('delivery_zone_id');
                }
            });

        $usageTokens = $usageQuery->sum('amount');

        $totalTokens = $purchaseTokens + $usageTokens;

        \Log::info('Tokens calculés avec debug détaillé', [
            'user_id' => $this->user_id,
            'zone_id' => $zoneId,
            'dakar_zone_id' => $dakarZoneId,
            'purchase_tokens' => $purchaseTokens,
            'usage_tokens' => $usageTokens,
            'total_tokens' => $totalTokens
        ]);

        return max(0, $totalTokens);
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
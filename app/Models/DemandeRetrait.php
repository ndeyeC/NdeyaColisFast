<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DemandeRetrait extends Model
{
    use HasFactory;

    protected $table = 'demandes_retrait';

    const STATUS_EN_ATTENTE = 'en_attente';
    const STATUS_APPROUVEE = 'approuvee';
    const STATUS_TRAITEE = 'traitee';
    const STATUS_REJETEE = 'rejetee';

    protected $fillable = [
        'user_id',
        'montant',
        'methode_paiement',
        'numero_paiement',
        'status',
        'motif_rejet',
        'date_traitement',
        'reference_transaction'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_traitement' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec l'utilisateur (livreur)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le livreur (alias pour user)
     */
    public function livreur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtenir les statuts disponibles
     */
    public static function getStatuts()
    {
        return [
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_APPROUVEE => 'Approuvée',
            self::STATUS_TRAITEE => 'Traitée',
            self::STATUS_REJETEE => 'Rejetée'
        ];
    }

    /**
     * Obtenir la couleur du badge selon le statut
     */
    public function getBadgeColorAttribute()
    {
        return [
            self::STATUS_EN_ATTENTE => 'warning',
            self::STATUS_APPROUVEE => 'info',
            self::STATUS_TRAITEE => 'success',
            self::STATUS_REJETEE => 'danger'
        ][$this->status] ?? 'secondary';
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatutLibelleAttribute()
    {
        return self::getStatuts()[$this->status] ?? 'Inconnu';
    }

    /**
     * Scope pour les demandes en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('status', self::STATUS_EN_ATTENTE);
    }

    /**
     * Scope pour les demandes traitées
     */
    public function scopeTraitees($query)
    {
        return $query->where('status', self::STATUS_TRAITEE);
    }

    /**
     * Scope pour un livreur donné
     */
    public function scopePourLivreur($query, $livreurId)
    {
        return $query->where('user_id', $livreurId);
    }

    /**
     * Marquer comme approuvée
     */
    public function approuver()
    {
        $this->update([
            'status' => self::STATUS_APPROUVEE,
            'date_traitement' => now()
        ]);
    }

    /**
     * Marquer comme traitée
     */
    public function traiter($referenceTransaction = null)
    {
        $this->update([
            'status' => self::STATUS_TRAITEE,
            'date_traitement' => now(),
            'reference_transaction' => $referenceTransaction
        ]);
    }

    /**
     * Rejeter la demande
     */
    public function rejeter($motif)
    {
        $this->update([
            'status' => self::STATUS_REJETEE,
            'motif_rejet' => $motif,
            'date_traitement' => now()
        ]);

        // Rembourser le montant au livreur
        $this->user->increment('solde_disponible', $this->montant);
    }
}
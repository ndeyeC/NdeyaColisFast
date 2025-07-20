<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = [
        'commande_id',
        'user_id',
        'driver_id',
        'note',
        'commentaire',
        'type_evaluation' // 'client' ou 'driver'
    ];

    protected $casts = [
        'note' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function commnande()
    {
        return $this->belongsTo(Commnande::class, 'commande_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Scopes
    public function scopeParClient($query)
    {
        return $query->where('type_evaluation', 'client');
    }

    public function scopeParDriver($query)
    {
        return $query->where('type_evaluation', 'driver');
    }

    public function scopeNotesPositives($query)
    {
        return $query->where('note', '>=', 4);
    }

    public function scopeNotesNegatives($query)
    {
        return $query->where('note', '<=', 2);
    }

    // Méthodes utilitaires
    public function isPositive(): bool
    {
        return $this->note >= 4;
    }

    public function isNegative(): bool
    {
        return $this->note <= 2;
    }

    public function getEtoiles(): string
    {
        $etoiles = '';
        $noteEntiere = floor($this->note);
        $reste = $this->note - $noteEntiere;

        // Étoiles pleines
        for ($i = 0; $i < $noteEntiere; $i++) {
            $etoiles .= '★';
        }

        // Étoile à moitié si nécessaire
        if ($reste >= 0.5) {
            $etoiles .= '☆';
            $noteEntiere++;
        }

        // Étoiles vides
        for ($i = $noteEntiere; $i < 5; $i++) {
            $etoiles .= '☆';
        }

        return $etoiles;
    }
}
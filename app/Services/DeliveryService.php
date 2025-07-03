<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryZone;
use App\Models\User;
use Exception;

class DeliveryService
{
    /**
     * Créer une nouvelle demande de livraison
     *
     * @param User $user L'utilisateur qui demande la livraison
     * @param array $data Les données de la livraison
     * @return Delivery
     * @throws Exception
     */
    public function createDelivery(User $user, array $data)
    {
        
        $zone = DeliveryZone::findOrFail($data['delivery_zone_id']);
        
        
        if (!$user->hasEnoughTokensForDelivery($zone->id)) {
            throw new Exception("Vous n'avez pas assez de jetons pour cette zone de livraison. Veuillez en acheter.");
        }
        
        // Créer la livraison
        $delivery = Delivery::create([
            'user_id' => $user->id,
            'delivery_zone_id' => $zone->id,
            'pickup_address' => $data['pickup_address'],
            'delivery_address' => $data['delivery_address'],
            'recipient_name' => $data['recipient_name'],
            'recipient_phone' => $data['recipient_phone'],
            'package_description' => $data['package_description'] ?? null,
            'package_weight' => $data['package_weight'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'status' => Delivery::STATUS_PENDING,
        ]);
        
        
        $user->debitTokensForDelivery(
            $zone->id, 
            1, 
            "Livraison #{$delivery->id} - {$delivery->pickup_address} → {$delivery->delivery_address}"
        );
        
        return $delivery;
    }
    
    /**
     * 
     *
     * @param Delivery 
     * @return Delivery
     */
    public function cancelDelivery(Delivery $delivery)
    {
        
        if (!in_array($delivery->status, [Delivery::STATUS_PENDING])) {
            throw new Exception("Cette livraison ne peut plus être annulée.");
        }
        
        
        $delivery->update([
            'status' => Delivery::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
        
        // Rembourser le jeton à l'utilisateur
        $user = $delivery->user;
        $user->tokens()->create([
            'amount' => 1,
            'delivery_zone_id' => $delivery->delivery_zone_id,
            'status' => 'completed',
            'reference' => 'REFUND-'.$delivery->id,
            'notes' => "Remboursement suite à l'annulation de la livraison #{$delivery->id}",
            'expiry_date' => now()->addWeek(), // Le jeton reste valide une semaine
        ]);
        
        return $delivery;
    }
}
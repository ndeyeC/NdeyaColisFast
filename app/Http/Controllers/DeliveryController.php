<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function createWithTokens(Request $request) {
        $request->validate([
            'pickup_address' => 'required',
            'destination_address' => 'required',
            'package_type' => 'required'
        ]);
        
        $user = $request->user();
        $deliveryCostInTokens = $this->calculateDeliveryCost($request->package_type);
        
        if ($user->token_balance < $deliveryCostInTokens) {
            return response()->json([
                'error' => 'Solde de jetons insuffisant',
                'required' => $deliveryCostInTokens,
                'current' => $user->token_balance
            ], 400);
        }
        
        $delivery = Delivery::create([
            'user_id' => $user->id,
            'pickup_address' => $request->pickup_address,
            'destination_address' => $request->destination_address,
            'package_type' => $request->package_type,
            'status' => 'pending',
            'cost_in_tokens' => $deliveryCostInTokens
        ]);
        
        $user->tokens()->create([
            'amount' => -$deliveryCostInTokens,
            'payment_method' => 'tokens',
            'status' => TokenTransaction::STATUS_COMPLETED,
            'reference' => 'DEL-' . $delivery->id
        ]);
        
        return response()->json([
            'message' => 'Livraison créée avec succès',
            'delivery' => $delivery,
            'new_balance' => $user->token_balance - $deliveryCostInTokens
        ]);
    }
    
    private function calculateDeliveryCost($packageType) {
        // Logique de calcul du coût en jetons
        $rates = [
            'small' => 100,   // 100 jetons = 1 000 FCFA
            'medium' => 150,  // 150 jetons = 1 500 FCFA
            'large' => 200    // 200 jetons = 2 000 FCFA
        ];
        
        return $rates[$packageType] ?? 100;
    }
}

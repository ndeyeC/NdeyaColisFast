<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function getBalance(Request $request) {
        return response()->json([
            'balance' => $request->user()->token_balance,
            'equivalent' => $request->user()->token_balance * 10 // 1 jeton = 10 FCFA
        ]);
    }
    
    public function purchase(Request $request) {
        $request->validate([
            'amount' => 'required|integer|min:100', // minimum 100 jetons
            'payment_method' => 'required|in:wave,orange_money,credit_card'
        ]);
        
        // Créer une transaction
        $transaction = $request->user()->tokens()->create([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => TokenTransaction::STATUS_PENDING,
            'reference' => 'TOK-' . uniqid()
        ]);
        
        // Ici vous intégrerez l'API de paiement (Wave, Orange Money, etc.)
        // Ceci est un exemple simplifié
        try {
            // Simulation de paiement réussi
            $paymentSuccess = $this->processPayment(
                $request->payment_method, 
                $request->amount * 10, // Montant en FCFA
                $transaction->reference
            );
            
            if ($paymentSuccess) {
                $transaction->update(['status' => TokenTransaction::STATUS_COMPLETED]);
                return response()->json(['message' => 'Achat de jetons réussi']);
            } else {
                $transaction->update(['status' => TokenTransaction::STATUS_FAILED]);
                return response()->json(['error' => 'Paiement échoué'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function history(Request $request) {
        $transactions = $request->user()->tokens()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return response()->json($transactions);
    }
    
    private function processPayment($method, $amount, $reference) {
        // Intégration réelle avec les APIs de paiement
        // Retourne true si le paiement réussit
        return true; // Simulation
    }
}

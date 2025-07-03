<?php

namespace App\Http\Controllers;

use App\Models\TokenTransaction;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class TokenController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = $user->tokens()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $zones = DeliveryZone::getAllZonesWithPrices();
        
        $validTokens = $user->tokens()
            ->where('status', TokenTransaction::STATUS_COMPLETED)
            ->where(function($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            })
            ->selectRaw('delivery_zone_id, SUM(amount) as total')
            ->groupBy('delivery_zone_id')
            ->get()
            ->keyBy('delivery_zone_id');
        
        return view('tokens.index', [
            'transactions' => $transactions,
            'zones' => $zones,
            'validTokens' => $validTokens
        ]);
    }
    
    public function getBalance()
    {
        $user = Auth::user();
        
        $validTokens = $user->tokens()
            ->where('status', TokenTransaction::STATUS_COMPLETED)
            ->where(function($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            })
            ->selectRaw('delivery_zone_id, SUM(amount) as total')
            ->groupBy('delivery_zone_id')
            ->get();
        
        return response()->json([
            'tokens' => $validTokens,
        ]);
    }
    
    public function purchase(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'zone_id' => 'required|exists:delivery_zones,id',
            'payment_method' => 'required|in:wave,orange_money,credit_card'
        ]);
        
        $user = $request->user();
        $zone = DeliveryZone::findOrFail($request->zone_id);
        
        $priceInFcfa = $zone->base_token_price * $request->amount;
        
        $expiryDate = Carbon::now()->addWeek();
        
        $transaction = $user->tokens()->create([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'delivery_zone_id' => $zone->id,
            'status' => TokenTransaction::STATUS_PENDING,
            'reference' => 'TOK-'.uniqid(),
            'expiry_date' => $expiryDate 
        ]);
        
        try {
            $paymentSuccess = $this->processPayment(
                $request->payment_method,
                $priceInFcfa,
                $transaction->reference
            );
            
            if ($paymentSuccess) {
                $transaction->update([
                    'status' => TokenTransaction::STATUS_COMPLETED,
                    'notes' => 'Paiement réussi - Expire le ' . $expiryDate->format('d/m/Y')
                ]);
                
                return redirect()->route('tokens.index')
                    ->with('success', "Achat de {$request->amount} jeton(s) pour {$zone->name} réussi !");
            }
            
            $transaction->update(['status' => TokenTransaction::STATUS_FAILED]);
            return back()->with('error', 'Échec du paiement');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    private function processPayment($method, $amount, $reference)
    {
        // Implément logique de paiement
        return true;
    }
}
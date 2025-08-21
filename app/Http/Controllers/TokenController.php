<?php

namespace App\Http\Controllers;

use App\Models\TokenTransaction;
use App\Models\DeliveryZone;
use App\Services\CinetPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TokenController extends Controller
{
    protected $cinetPayService;

    public function __construct(CinetPayService $cinetPayService)
    {
        $this->cinetPayService = $cinetPayService;
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour voir vos jetons.');
        }

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

        return view('tokens.index', compact('transactions', 'zones', 'validTokens'));
    }


   
    public function purchase(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        \Log::error('Utilisateur non authentifié dans purchase');
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour acheter des jetons.');
    }

    $request->validate([
        'amount' => 'required|integer|min:1',
        'zone_id' => 'required|exists:delivery_zones,id',
    ]);

    $zone = DeliveryZone::findOrFail($request->zone_id);
    $expiryDate = Carbon::now()->addWeek();

    $transaction = $user->tokens()->create([
        'amount' => $request->amount,
        'payment_method' => 'cinetpay',
        'delivery_zone_id' => $zone->id,
        'status' => TokenTransaction::STATUS_PENDING,
        'reference' => 'TOK-' . uniqid(),
        'expiry_date' => $expiryDate,
        'type' => 'achat',
    ]);

    return $this->redirectToCinetPayForTokens($transaction, $user);
}

    private function redirectToCinetPayForTokens(TokenTransaction $transaction, $user)
    {
        try {
            $successUrl = rtrim(config('app.url'), '/') . route('tokens.payment.success', [], false);
            $ipnUrl = rtrim(config('app.url'), '/') . route('tokens.payment.ipn', [], false);
            $cancelUrl = rtrim(config('app.url'), '/') . route('tokens.payment.cancel', [], false);

            \Log::info('Generated URLs:', [
                'success_url' => $successUrl,
                'ipn_url' => $ipnUrl,
                'cancel_url' => $cancelUrl
            ]);

            $priceInFcfa = $transaction->amount * $transaction->zone->base_token_price;

            $paymentData = [
                'item_name' => "Achat jetons {$transaction->reference}",
                'item_price' => $priceInFcfa,
                'ref_command' => $transaction->reference,
                'success_url' => $successUrl,
                'ipn_url' => $ipnUrl,
                'cancel_url' => $cancelUrl,
                'custom_field' => [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id
                ],
                'customer_name' => $user->name ?? '',
                'customer_email' => $user->email ?? '',
                'customer_phone' => $user->phone ?? '',
                'customer_address' => '',
                'customer_city' => '',
                'customer_country' => 'SN',
                'customer_state' => '',
                'customer_zip' => ''
            ];

            \Log::info('Payment Data:', $paymentData);

            $response = $this->cinetPayService->createPaymentRequest($paymentData);

            \Log::info('CinetPay Response:', $response);

            if (!$response['success']) {
                throw new \Exception($response['message']);
            }

            if (empty($response['data']['redirect_url'])) {
                throw new \Exception('URL de redirection CinetPay non fournie');
            }

            return redirect($response['data']['redirect_url']);
        } catch (\Exception $e) {
            \Log::error('CinetPay Redirect Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la redirection vers le paiement : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function paymentIpn(Request $request)
    {
        $data = $request->all();

        \Log::info('IPN Data Received:', $data);

        if ($this->cinetPayService->verifySignature($data)) {
            $transaction = TokenTransaction::where('reference', $data['cpm_trans_id'])->first();

            if ($transaction && $transaction->status !== TokenTransaction::STATUS_COMPLETED) {
                $statusResponse = $this->cinetPayService->checkPaymentStatus($data['cpm_trans_id']);
                
                if ($statusResponse['success'] && $statusResponse['data']['payment_status'] === 'completed') {
                    $transaction->update(['status' => TokenTransaction::STATUS_COMPLETED]);
                    return response('OK', 200);
                }
            }
        }

        return response('Invalid request', 400);
    }

    public function paymentSuccess()
    {
        return view('tokens.payment_success');
    }

    public function paymentCancel()
    {
        return view('tokens.payment_cancel');
    }
}
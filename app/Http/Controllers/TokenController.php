<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\TokenTransaction;
use App\Models\DeliveryZone;
use App\Services\PayDunyaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TokenController extends Controller
{
    protected PayDunyaService $payDunyaService;

    public function __construct(PayDunyaService $payDunyaService)
    {
        $this->payDunyaService = $payDunyaService;
    }

    // ================= AFFICHAGE DES TRANSACTIONS =================
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour voir vos jetons.');
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

    // ================= ACHAT DE JETONS =================
    public function purchase(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('Utilisateur non authentifié dans purchase');
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour acheter des jetons.');
        }

        // Validation des données
        $request->validate([
            'amount' => 'required|integer|min:1',
            'zone_id' => 'required|exists:delivery_zones,id',
        ]);

        // Valider le numéro de téléphone
        $phone = preg_replace('/[^0-9]/', '', $user->numero_telephone ?? '');
        if (empty($phone) || !preg_match('/^[0-9]{9,10}$/', $phone)) {
            Log::error('Numéro de téléphone invalide pour l\'utilisateur', [
                'user_id' => $user->user_id ?? $user->id,
                'phone' => $user->numero_telephone ?? 'vide'
            ]);
            return redirect()->back()
                ->with('error', 'Veuillez ajouter un numéro de téléphone valide (9-10 chiffres) dans votre profil.');
        }

        // Créer la transaction
        try {
            $zone = DeliveryZone::findOrFail($request->zone_id);
            $expiryDate = Carbon::now()->addWeek();

            $transaction = $user->tokens()->create([
                'amount' => $request->amount,
                'payment_method' => 'paydunya',
                'delivery_zone_id' => $zone->id,
                'status' => TokenTransaction::STATUS_PENDING,
                'reference' => 'TOK-' . uniqid(),
                'expiry_date' => $expiryDate,
                'type' => TokenTransaction::TYPE_ACHAT,
            ]);

            Log::info('Transaction créée', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->user_id ?? $user->id,
                'amount' => $request->amount,
                'zone_id' => $zone->id
            ]);

            return $this->redirectToPayDunyaForTokens($transaction, $user);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la transaction', [
                'error' => $e->getMessage(),
                'user_id' => $user->user_id ?? $user->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la transaction : ' . $e->getMessage())
                ->withInput();
        }
    }

    // ================= REDIRECTION PAYDUNYA =================
    private function redirectToPayDunyaForTokens(TokenTransaction $transaction, $user)
    {
        try {
            // Vérifier la configuration PayDunya
            if (!$this->payDunyaService->isConfigured()) {
                throw new \Exception('Configuration PayDunya incomplète');
            }

            // URLs de callback
            $successUrl = route('tokens.payment.success', ['transaction_id' => $transaction->id]);
            $ipnUrl = route('tokens.payment.ipn');
            $cancelUrl = route('tokens.payment.cancel');

            // Valider le téléphone encore une fois
            $phone = preg_replace('/[^0-9]/', '', $user->numero_telephone ?? '');
            if (empty($phone) || !preg_match('/^[0-9]{9,10}$/', $phone)) {
                throw new \Exception('Numéro de téléphone invalide');
            }

            // Calculer le prix total
            $tokenPrice = $transaction->zone->base_token_price ?? 100; // Prix par défaut
            $totalPrice = $transaction->amount * $tokenPrice;

            // Données pour PayDunya
            $paymentData = [
                'item_name' => "Achat de {$transaction->amount} jetons - {$transaction->reference}",
                'item_price' => $totalPrice,
                'ref_command' => $transaction->reference,
                'success_url' => $successUrl,
                'ipn_url' => $ipnUrl,
                'cancel_url' => $cancelUrl,
                'custom_field' => [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->user_id ?? $user->id,
                    'type' => 'token_purchase'
                ],
                'customer_name' => $user->name ?? 'Client',
                'customer_email' => $user->email ?? '',
                'customer_phone' => $phone,
            ];

            Log::info('Envoi des données à PayDunya', [
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
                'amount' => $totalPrice,
                'customer_phone' => $phone
            ]);

            // Créer la demande de paiement
            $response = $this->payDunyaService->createPaymentRequest($paymentData);

            Log::debug('Réponse de PayDunya', [
                'transaction_id' => $transaction->id,
                'success' => $response['success'] ?? false,
                'status_code' => $response['status_code'] ?? 'N/A',
                'has_redirect_url' => isset($response['data']['redirect_url'])
            ]);

            // Vérifier la réponse
            if ($response['success'] && isset($response['data']['redirect_url'])) {
                // Sauvegarder le token si disponible
                if (!empty($response['data']['token'])) {
                    $transaction->setPayDunyaToken($response['data']['token']);
                }

                Log::info('Redirection vers PayDunya réussie', [
                    'transaction_id' => $transaction->id,
                    'redirect_url' => $response['data']['redirect_url']
                ]);

                return redirect($response['data']['redirect_url']);
            }

            // Échec de la création du paiement
            $errorMessage = $response['message'] ?? 'Erreur PayDunya inconnue';
            throw new \Exception($errorMessage);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la redirection PayDunya', [
                'transaction_id' => $transaction->id ?? null,
                'user_id' => $user->user_id ?? $user->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Marquer la transaction comme échouée
            if (isset($transaction)) {
                $transaction->markAsFailed('Erreur redirection PayDunya: ' . $e->getMessage());
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'initialisation du paiement : ' . $e->getMessage())
                ->withInput();
        }
    }

    // ================= CALLBACK IPN =================
    public function paymentIpn(Request $request)
    {
        $data = $request->all();

        Log::info('IPN PayDunya reçu', [
            'data_keys' => array_keys($data),
            'has_signature' => isset($data['signature']),
            'has_data' => isset($data['data'])
        ]);

        try {
            // Vérifier la signature
            if (!$this->payDunyaService->verifySignature($data)) {
                Log::warning('IPN PayDunya: Signature invalide');
                return response('Invalid signature', 400);
            }

            // Traiter les données IPN
            $processedData = $this->payDunyaService->processIpnData($data);
            
            if (!$processedData) {
                Log::error('IPN PayDunya: Données invalides');
                return response('Invalid data', 400);
            }

            $invoiceRef = $processedData['invoice_ref'];
            $paydunyaToken = $processedData['token'];

            if (!$invoiceRef) {
                Log::error('IPN PayDunya: Référence manquante');
                return response('Missing reference', 400);
            }

            // Trouver la transaction
            $transaction = TokenTransaction::where('reference', $invoiceRef)->first();

            if (!$transaction) {
                Log::error('IPN PayDunya: Transaction non trouvée', ['reference' => $invoiceRef]);
                return response('Transaction not found', 404);
            }

            // Éviter le double traitement
            if ($transaction->status === TokenTransaction::STATUS_COMPLETED) {
                Log::info('IPN PayDunya: Transaction déjà traitée', ['transaction_id' => $transaction->id]);
                return response('Already processed', 200);
            }

            // Sauvegarder le token PayDunya
            if ($paydunyaToken && empty($transaction->paydunya_token)) {
                $transaction->setPayDunyaToken($paydunyaToken);
            }

            // Vérifier le statut du paiement
            $statusResponse = $this->payDunyaService->checkPaymentStatus($paydunyaToken);
            
            if ($statusResponse['success'] && $statusResponse['data']['payment_status'] === 'completed') {
                $transaction->markAsCompleted();
                
                Log::info('IPN PayDunya: Paiement confirmé', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount
                ]);
                
                return response('OK', 200);
            } else {
                $status = $statusResponse['data']['payment_status'] ?? 'inconnu';
                $transaction->markAsFailed("Paiement non confirmé: {$status}");
                
                Log::warning('IPN PayDunya: Paiement non confirmé', [
                    'transaction_id' => $transaction->id,
                    'status' => $status
                ]);
            }

            return response('Payment not confirmed', 400);

        } catch (\Exception $e) {
            Log::error('Erreur IPN PayDunya', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            return response('Internal error', 500);
        }
    }

    // ================= PAGE SUCCÈS =================
    public function paymentSuccess(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        
        if (!$transactionId) {
            Log::warning('Page succès: ID transaction manquant');
            return redirect()->route('tokens.index')
                ->with('error', 'Transaction introuvable');
        }

        $transaction = TokenTransaction::find($transactionId);
        
        if (!$transaction) {
            Log::error('Page succès: Transaction non trouvée', ['transaction_id' => $transactionId]);
            return redirect()->route('tokens.index')
                ->with('error', 'Transaction introuvable');
        }

        // Vérifier que l'utilisateur connecté est propriétaire de la transaction
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id;
        
        if ($transaction->user_id !== $userId) {
            Log::warning('Page succès: Tentative d\'accès non autorisé', [
                'transaction_id' => $transactionId,
                'transaction_user_id' => $transaction->user_id,
                'current_user_id' => $userId
            ]);
            
            return redirect()->route('tokens.index')
                ->with('error', 'Accès non autorisé');
        }

        // Si déjà complétée, afficher la page de succès
        if ($transaction->status === TokenTransaction::STATUS_COMPLETED) {
            Log::info('Page succès: Transaction déjà complétée', ['transaction_id' => $transaction->id]);
            return view('tokens.payment_success', compact('transaction'));
        }

        // Vérifier le statut auprès de PayDunya
        try {
            $statusResponse = $this->payDunyaService->checkPaymentStatus($transaction->paydunya_token);
            
            if ($statusResponse['success'] && $statusResponse['data']['payment_status'] === 'completed') {
                $transaction->markAsCompleted();
                
                Log::info('Page succès: Paiement validé', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $userId
                ]);
                
                return view('tokens.payment_success', compact('transaction'));
            }

            // Paiement non validé
            $status = $statusResponse['data']['payment_status'] ?? 'inconnu';
            $transaction->markAsFailed("Paiement non validé: {$status}");
            
            Log::warning('Page succès: Paiement non validé', [
                'transaction_id' => $transaction->id,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Page succès: Erreur vérification statut', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('tokens.index')
            ->with('error', 'Le paiement n\'a pas été validé. Veuillez contacter le support si le problème persiste.');
    }

    // ================= PAGE ANNULATION =================
    public function paymentCancel(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        
        if ($transactionId) {
            $transaction = TokenTransaction::find($transactionId);
            
            if ($transaction && $transaction->status === TokenTransaction::STATUS_PENDING) {
                $transaction->markAsFailed('Paiement annulé par l\'utilisateur');
                
                Log::info('Paiement annulé', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id
                ]);
            }
        }

        return view('tokens.payment_cancel');
    }
}
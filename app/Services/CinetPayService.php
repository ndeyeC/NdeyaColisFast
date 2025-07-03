<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CinetPayService
{
    protected $apiKey;
    protected $siteId;
    protected $secretKey;
    protected $baseUrl;
    protected $currency;
    protected $env;
    protected $timeout = 30;

    public function __construct()
    {
        $this->apiKey = config('cinetpay.api_key');
        $this->siteId = config('cinetpay.site_id');
        $this->secretKey = config('cinetpay.secret_key');
        $this->baseUrl = rtrim(config('cinetpay.base_url', 'https://api-checkout.cinetpay.com'), '/');
        $this->currency = config('cinetpay.currency', 'XOF');
        $this->env = config('cinetpay.env', 'test');

        // Vérification des credentials
        if (empty($this->apiKey) || empty($this->siteId) || empty($this->secretKey)) {
            Log::error('CinetPay configuration error: Missing API key, site ID or secret key');
        }
    }

    public function createPaymentRequest(array $data): array
    {
        try {
            // Validation des données requises
            if (!isset($data['item_price']) || $data['item_price'] <= 0) {
                return [
                    'success' => false,
                    'message' => 'Prix invalide',
                    'status_code' => 422
                ];
            }

            if (empty($data['ref_command'])) {
                return [
                    'success' => false,
                    'message' => 'Référence de commande obligatoire',
                    'status_code' => 422
                ];
            }

            // Vérifier les credentials
            if (empty($this->apiKey) || empty($this->siteId)) {
                Log::error('CinetPay credentials missing', [
                    'api_key_set' => !empty($this->apiKey),
                    'site_id_set' => !empty($this->siteId)
                ]);
                return [
                    'success' => false,
                    'message' => 'Configuration CinetPay incomplète',
                    'status_code' => 500
                ];
            }

            // Préparer le payload CinetPay selon la documentation officielle
            $payload = [
                'amount' => (int)$data['item_price'], // Montant en FCFA
                'currency' => $this->currency,
                'transaction_id' => $data['ref_command'],
                'description' => $data['item_name'] ?? 'Commande',
                'return_url' => $data['success_url'] ?? '',
                'notify_url' => $data['ipn_url'] ?? '',
                'cancel_url' => $data['cancel_url'] ?? '',
                'customer_name' => $data['customer_name'] ?? '',
                'customer_surname' => $data['customer_surname'] ?? '',
                'customer_email' => $data['customer_email'] ?? '',
                'customer_phone_number' => $data['customer_phone'] ?? '',
                'customer_address' => $data['customer_address'] ?? '',
                'customer_city' => $data['customer_city'] ?? '',
                'customer_country' => $data['customer_country'] ?? 'SN',
                'customer_state' => $data['customer_state'] ?? '',
                'customer_zip_code' => $data['customer_zip'] ?? '',
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'channels' => 'ALL',
                'metadata' => is_array($data['custom_field'] ?? null) 
                    ? json_encode($data['custom_field']) 
                    : ($data['custom_field'] ?? '{}')
            ];

            // Log pour debug (masquer les données sensibles)
            Log::info('CinetPay payment request', [
                'endpoint' => $this->baseUrl . '/v2/payment',
                'amount' => $payload['amount'],
                'currency' => $payload['currency'],
                'transaction_id' => $payload['transaction_id'],
                'site_id_present' => !empty($payload['site_id']),
                'api_key_present' => !empty($payload['apikey'])
            ]);

            // Faire la requête HTTP
            $response = Http::withOptions([
                'verify' => app()->environment('production'),
                'timeout' => $this->timeout,
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/v2/payment', $payload);

            // Log de la réponse complète
            Log::info('CinetPay raw response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            $responseBody = $response->json() ?: [];

            // Vérifier la réponse selon la documentation CinetPay
            if ($response->successful() && isset($responseBody['code'])) {
                // Code 201 = succès pour l'initialisation du paiement
                if ($responseBody['code'] == '201') {
                    return [
                        'success' => true,
                        'message' => $responseBody['message'] ?? 'Paiement initialisé',
                        'data' => [
                            'token' => $responseBody['data']['payment_token'] ?? null,
                            'redirect_url' => $responseBody['data']['payment_url'] ?? null,
                            'transaction_id' => $responseBody['data']['payment_token'] ?? $data['ref_command'],
                            'raw_response' => $responseBody
                        ],
                        'status_code' => $response->status()
                    ];
                } else {
                    // Code d'erreur CinetPay
                    $message = $responseBody['message'] ?? 'Erreur CinetPay';
                    $errors = $responseBody['data'] ?? [];
                    
                    Log::error('CinetPay payment failed', [
                        'response_code' => $responseBody['code'],
                        'message' => $message,
                        'errors' => $errors,
                        'full_response' => $responseBody
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => $message,
                        'errors' => $errors,
                        'status_code' => $response->status(),
                        'raw_response' => $responseBody
                    ];
                }
            } else {
                // Erreur HTTP ou réponse malformée
                $message = $responseBody['message'] ?? 'Erreur de communication avec CinetPay';
                
                Log::error('CinetPay HTTP error', [
                    'http_status' => $response->status(),
                    'response_body' => $responseBody,
                    'is_successful' => $response->successful()
                ]);
                
                return [
                    'success' => false,
                    'message' => $message,
                    'status_code' => $response->status(),
                    'raw_response' => $responseBody
                ];
            }

        } catch (\Exception $e) {
            Log::error('CinetPay exception: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erreur système: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function checkPaymentStatus(string $transactionId): array
    {
        try {
            if (empty($this->apiKey) || empty($this->siteId)) {
                return [
                    'success' => false,
                    'message' => 'CinetPay API credentials are not configured',
                    'status_code' => 500
                ];
            }

            if (empty($transactionId)) {
                return [
                    'success' => false,
                    'message' => 'Transaction ID is required',
                    'status_code' => 400
                ];
            }

            $payload = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId
            ];

            $response = Http::withOptions([
                'verify' => app()->environment('production'),
                'timeout' => $this->timeout,
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/v2/payment/check', $payload);

            Log::info('CinetPay status check response', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            $body = $response->json() ?: [];

            if (!$response->successful()) {
                Log::warning('CinetPay status check failed', [
                    'status' => $response->status(),
                    'body' => $body
                ]);
                return [
                    'success' => false,
                    'message' => 'Status check failed: ' . ($body['message'] ?? 'Unknown error'),
                    'status_code' => $response->status()
                ];
            }

            // CinetPay retourne code '00' pour succès
            $isSuccess = isset($body['code']) && $body['code'] == '00';
            
            return [
                'success' => $isSuccess,
                'message' => $body['message'] ?? 'Status check completed',
                'data' => array_merge($body, [
                    'payment_status' => $this->mapCinetPayStatus($body['data']['status'] ?? 'PENDING'),
                    'custom' => $body['data']['metadata'] ?? '{}'
                ]),
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('CinetPay status error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return [
                'success' => false,
                'message' => 'Status error: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Vérifier la signature des notifications IPN
     */
    public function verifySignature(array $data): bool
    {
        try {
            // En mode test/développement, désactiver la vérification
            if (app()->environment('local', 'testing')) {
                Log::info('Signature verification disabled in local/testing environment');
                return true;
            }

            if (!isset($data['cpm_trans_id']) || !isset($data['signature'])) {
                Log::warning('Missing signature data in IPN', $data);
                return false;
            }

            // Construire la chaîne à signer selon la documentation CinetPay
            $signString = $data['cpm_site_id'] . $data['cpm_trans_id'] . $data['cpm_trans_date'] . 
                         $data['cpm_amount'] . $data['cpm_currency'] . $data['signature'] . $this->secretKey;
            
            $expectedSignature = md5($signString);
            
            $isValid = hash_equals($expectedSignature, $data['signature']);
            
            Log::info('Signature verification', [
                'provided_signature' => $data['signature'],
                'expected_signature' => $expectedSignature,
                'is_valid' => $isValid
            ]);
            
            return $isValid;
        } catch (\Exception $e) {
            Log::error('CinetPay signature verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mapper les statuts CinetPay vers nos statuts internes
     */
    protected function mapCinetPayStatus(string $status): string
    {
        $statusMap = [
            'ACCEPTED' => 'completed',
            'REFUSED' => 'failed',
            'PENDING' => 'pending',
            'CANCELLED' => 'cancelled'
        ];

        return $statusMap[$status] ?? 'pending';
    }
}
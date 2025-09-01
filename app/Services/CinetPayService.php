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
        // Validation des données requises (code existant...)
        
        // NOUVELLE VALIDATION DES CREDENTIALS
        if (empty($this->masterKey) || empty($this->privateKey) || empty($this->token)) {
            Log::error('PayDunya credentials validation failed', [
                'master_key_empty' => empty($this->masterKey),
                'private_key_empty' => empty($this->privateKey),
                'token_empty' => empty($this->token),
                'master_key_length' => strlen($this->masterKey ?? ''),
                'private_key_length' => strlen($this->privateKey ?? ''),
                'token_length' => strlen($this->token ?? ''),
            ]);
            
            return [
                'success' => false,
                'message' => 'Configuration PayDunya incomplète - Vérifiez vos clés API',
                'status_code' => 500
            ];
        }

        // Validation du format des clés
        if (strlen($this->masterKey) < 10 || strlen($this->privateKey) < 10 || strlen($this->token) < 10) {
            Log::error('PayDunya credentials seem too short', [
                'master_key_length' => strlen($this->masterKey),
                'private_key_length' => strlen($this->privateKey),
                'token_length' => strlen($this->token),
            ]);
            
            return [
                'success' => false,
                'message' => 'Format des clés PayDunya invalide',
                'status_code' => 500
            ];
        }

        // Reste du code existant...
        
        // Headers PayDunya avec log détaillé
        $headers = [
            'PAYDUNYA-MASTER-KEY' => $this->masterKey,
            'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
            'PAYDUNYA-TOKEN' => $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        Log::info('PayDunya request headers prepared', [
            'master_key_used' => substr($this->masterKey, 0, 10) . '...',
            'private_key_used' => substr($this->privateKey, 0, 10) . '...',
            'token_used' => substr($this->token, 0, 10) . '...',
            'mode' => $this->mode,
        ]);

        // Faire la requête HTTP (code existant...)
        
    } catch (\Exception $e) {
        Log::error('PayDunya exception: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'credentials_present' => [
                'master_key' => !empty($this->masterKey),
                'private_key' => !empty($this->privateKey),
                'token' => !empty($this->token),
            ]
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
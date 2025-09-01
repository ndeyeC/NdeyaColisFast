<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayDunyaService
{
    protected $masterKey;
    protected $privateKey;
    protected $publicKey;
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->masterKey = config('paydunya.master_key');
        $this->privateKey = config('paydunya.private_key');
        $this->publicKey = config('paydunya.public_key');
        $this->token = config('paydunya.token');
        $this->baseUrl = config('paydunya.mode') === 'live' 
            ? 'https://app.paydunya.com/api/v1' 
            : 'https://app.paydunya.com/sandbox-api/v1';

        Log::info('PayDunya configuration loaded', [
            'app_url' => config('app.url'),
            'mode' => config('paydunya.mode'),
            'base_url' => $this->baseUrl,
            'master_key_set' => !empty($this->masterKey),
            'private_key_set' => !empty($this->privateKey),
            'public_key_set' => !empty($this->publicKey),
            'token_set' => !empty($this->token),
            'private_key_prefix' => substr($this->privateKey, 0, 5)
        ]);
    }

    private function getHeaders()
    {
        return [
            'PAYDUNYA-MASTER-KEY' => $this->masterKey,
            'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
            'PAYDUNYA-PUBLIC-KEY' => $this->publicKey,
            'PAYDUNYA-TOKEN' => $this->token,
            'Content-Type' => 'application/json',
        ];
    }

    public function createPaymentRequest(array $data): array
    {
        try {
            // Validation des données
            if (empty($data['item_price']) || $data['item_price'] <= 0) {
                return ['success' => false, 'message' => 'Le montant doit être supérieur à 0', 'status_code' => 422];
            }

            if (empty($data['customer_email']) || !filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Email client invalide', 'status_code' => 422];
            }

            if (empty($data['customer_phone']) || !preg_match('/^[0-9]{9,10}$/', $data['customer_phone'])) {
                return ['success' => false, 'message' => 'Téléphone client invalide', 'status_code' => 422];
            }

            $payload = [
                'invoice' => [
                    'total_amount' => $data['item_price'],
                    'description' => $data['item_name'],
                    'invoice_ref' => $data['ref_command'],
                ],
                'store' => [
                    'name' => config('app.name', 'Colisfast'),
                    'website_url' => config('app.url'),
                ],
                'actions' => [
                    'callback_url' => $data['ipn_url'],
                    'return_url' => $data['success_url'],
                    'cancel_url' => $data['cancel_url'],
                ],
                'customer' => [
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                    'phone' => $data['customer_phone'],
                ],
                'custom_data' => $data['custom_field'] ?? [],
            ];

            Log::debug('PayDunya request payload', ['payload' => $payload]);

            $response = Http::withHeaders($this->getHeaders())->post("{$this->baseUrl}/checkout-invoice/create", $payload);
            
            $responseData = $response->json() ?? [];
            
            Log::debug('PayDunya API response:', [
                'status' => $response->status(),
                'data' => $responseData
            ]);

            // Vérifier le succès de la réponse
            if ($response->successful()) {
                $responseCode = $responseData['response_code'] ?? null;
                
                if ($responseCode === '00') {
                    // Chercher l'URL de redirection dans différents champs possibles
                    $redirectUrl = $responseData['response_text'] ?? 
                                  $responseData['invoice_url'] ?? 
                                  $responseData['checkout_url'] ?? 
                                  $responseData['redirect_url'] ?? 
                                  null;
                    
                    if ($redirectUrl && filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                        return [
                            'success' => true,
                            'data' => [
                                'redirect_url' => $redirectUrl,
                                'token' => $responseData['token'] ?? null,
                                'invoice_token' => $responseData['invoice_token'] ?? null,
                            ],
                            'status_code' => 200,
                            'raw_response' => $responseData
                        ];
                    }
                    
                    Log::error('PayDunya: URL de redirection non trouvée', ['response' => $responseData]);
                    return [
                        'success' => false,
                        'message' => 'URL de redirection non trouvée dans la réponse',
                        'status_code' => 422,
                        'raw_response' => $responseData
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $responseData['response_text'] ?? 'Erreur PayDunya: Code ' . $responseCode,
                    'status_code' => $response->status(),
                    'raw_response' => $responseData
                ];
            }

            // Erreur HTTP
            return [
                'success' => false,
                'message' => $responseData['response_text'] ?? $responseData['message'] ?? 'Erreur PayDunya HTTP ' . $response->status(),
                'status_code' => $response->status(),
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('PayDunya exception dans createPaymentRequest:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erreur PayDunya : ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function checkPaymentStatus($paydunyaToken): array
    {
        try {
            if (empty($paydunyaToken)) {
                return [
                    'success' => false,
                    'message' => 'Token PayDunya requis',
                    'status_code' => 400
                ];
            }

            $response = Http::withHeaders($this->getHeaders())
                           ->get("{$this->baseUrl}/checkout-invoice/confirm/{$paydunyaToken}");
            
            $responseData = $response->json() ?? [];
            
            Log::debug('PayDunya status check response:', [
                'status' => $response->status(),
                'data' => $responseData
            ]);

            if ($response->successful()) {
                $responseCode = $responseData['response_code'] ?? null;
                
                if ($responseCode === '00') {
                    $status = $responseData['status'] ?? 'unknown';
                    
                    return [
                        'success' => true,
                        'data' => [
                            'payment_status' => $status === 'completed' ? 'completed' : 'pending',
                            'invoice_data' => $responseData
                        ],
                        'status_code' => 200
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $responseData['response_text'] ?? 'Erreur lors de la vérification: Code ' . $responseCode,
                    'status_code' => $response->status()
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['response_text'] ?? $responseData['message'] ?? 'Erreur lors de la vérification du statut',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut PayDunya:', [
                'error' => $e->getMessage(),
                'token' => $paydunyaToken
            ]);
            
            return [
                'success' => false,
                'message' => 'Erreur PayDunya : ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Vérifie la signature des données IPN de PayDunya
     */
    public function verifySignature(array $data): bool
    {
        try {
            // PayDunya utilise généralement une signature HMAC
            $signature = $data['signature'] ?? null;
            $dataToVerify = $data['data'] ?? '';
            
            if (empty($signature) || empty($dataToVerify)) {
                Log::warning('PayDunya IPN: Signature ou données manquantes', [
                    'has_signature' => !empty($signature),
                    'has_data' => !empty($dataToVerify)
                ]);
                return false;
            }

            // Calculer la signature attendue avec votre clé privée
            $expectedSignature = hash_hmac('sha256', $dataToVerify, $this->privateKey);
            
            $isValid = hash_equals($expectedSignature, $signature);
            
            Log::debug('PayDunya signature verification', [
                'is_valid' => $isValid,
                'provided_signature' => substr($signature, 0, 10) . '...',
                'expected_signature' => substr($expectedSignature, 0, 10) . '...'
            ]);
            
            return $isValid;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification de signature PayDunya:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Traite les données IPN reçues de PayDunya
     */
    public function processIpnData(array $data): ?array
    {
        try {
            $invoiceData = json_decode($data['data'] ?? '{}', true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('PayDunya IPN: Données JSON invalides', [
                    'json_error' => json_last_error_msg(),
                    'data' => $data['data'] ?? null
                ]);
                return null;
            }

            return [
                'invoice_ref' => $invoiceData['invoice']['invoice_ref'] ?? null,
                'token' => $invoiceData['invoice']['token'] ?? null,
                'status' => $invoiceData['invoice']['status'] ?? null,
                'amount' => $invoiceData['invoice']['total_amount'] ?? null,
                'customer' => $invoiceData['customer'] ?? [],
                'custom_data' => $invoiceData['custom_data'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement des données IPN:', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return null;
        }
    }

    /**
     * Vérifie si les clés API sont configurées
     */
    public function isConfigured(): bool
    {
        return !empty($this->masterKey) && 
               !empty($this->privateKey) && 
               !empty($this->publicKey) && 
               !empty($this->token);
    }
}
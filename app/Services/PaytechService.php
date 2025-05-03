<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaytechService
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl;
    protected $currency;
    protected $env;
    protected $timeout = 30;

    public function __construct()
    {
        $this->apiKey = config('paytech.api_key');
        $this->apiSecret = config('paytech.api_secret');
        $this->baseUrl = rtrim(config('paytech.base_url'), '/');
        $this->currency = config('paytech.currency');
        $this->env = config('paytech.env');

        if (empty($this->apiKey) || empty($this->apiSecret)) {
            Log::warning('Configuration PayTech incomplète! Clés API manquantes.');
        }

        if (empty($this->baseUrl)) {
            $this->baseUrl = 'https://paytech.sn/api/payment/v1';
            Log::warning('URL de base PayTech non configurée, valeur par défaut utilisée: ' . $this->baseUrl);
        }
    }

    public function createPaymentRequest($data)
    {
        $data = $this->validatePaymentData($data);
        $this->testConnectivity();

        try {
            $endpoint = "{$this->baseUrl}/payment/request-payment";

            $customField = $data['custom_field'] ?? [];
            if (!is_string($customField)) {
                $customField = json_encode($customField);
            }



            // $payload = [
            //     'item_name' => substr($data['item_name'] ?? 'Commande', 0, 100),
            //     'item_price' => number_format((float)$data['item_price'], 2, '.', ''),
            //     'currency' => $this->currency,
            //     'ref_command' => (string)($data['ref_command'] ?? uniqid('cmd_')),
            //     'command_name' => substr($data['command_name'] ?? 'Commande', 0, 50),
            //     'env' => $this->env,
            //     'success_url' => $this->validateUrl($data['success_url']),
            //     'ipn_url' => $this->validateUrl($data['ipn_url']),
            //     'cancel_url' => $this->validateUrl($data['cancel_url']),
            //     'custom_field' => $customField
            // ];

            // Log::info('Requête PayTech:', [
            //     'endpoint' => $endpoint,
            //     'payload' => $payload,
            //     'headers' => [
            //         'API_KEY' => '***' . substr($this->apiKey, -4),
            //         'Content-Type' => 'application/json'
            //     ]
            // ]);

            $ipnUrl = $this->validateUrl(config('paytech.ipn_url'));


            $payload = [
                'item_name' => substr($data['item_name'] ?? 'Commande', 0, 100),
                'item_price' => number_format((float)$data['item_price'], 2, '.', ''),
                'currency' => $this->currency,
                'ref_command' => (string)($data['ref_command'] ?? uniqid('cmd_')),
                'command_name' => substr($data['command_name'] ?? 'Commande', 0, 50),
                'env' => $this->env,
                'success_url' => $this->validateUrl($data['success_url']),
                'ipn_url' => $ipnUrl,  // Utilisation de l'URL IPN validée

                // 'ipn_url' => config('paytech.ipn_url'),  // Utilisation de la configuration
                'cancel_url' => $this->validateUrl($data['cancel_url']),
                'custom_field' => $customField
            ];
            

            $response = $this->makeJsonRequest($endpoint, $payload);

            if (!$response || isset($response['success']) && $response['success'] === false) {
                Log::warning('Tentative JSON échouée, essai avec form-urlencoded...');
                $response = $this->makeFormRequest($endpoint, $payload);
            }

            return $this->normalizeResponse($response);
        } catch (\Exception $e) {
            Log::error('ERREUR PAYTECH COMPLETE:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la requête: ' . $e->getMessage(),
                'error' => [$e->getMessage()]
            ];
        }
    }

    private function makeJsonRequest($endpoint, $payload)
    {
        try {
            $response = Http::withOptions([
                'verify' => $this->shouldVerifySsl(),
                'timeout' => $this->timeout,
            ])->withHeaders([
                'API_KEY' => $this->apiKey,
                'API_SECRET' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->post($endpoint, $payload);

            Log::info('Réponse JSON brute:', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            return $this->processResponse($response);
        } catch (\Exception $e) {
            Log::error('Erreur requête JSON:', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function makeFormRequest($endpoint, $payload)
    {
        try {
            foreach ($payload as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $payload[$key] = json_encode($value);
                }
            }

            $response = Http::withOptions([
                'verify' => $this->shouldVerifySsl(),
                'timeout' => $this->timeout,
            ])->withHeaders([
                'API_KEY' => $this->apiKey,
                'API_SECRET' => $this->apiSecret,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($endpoint, $payload);

            Log::info('Réponse FORM brute:', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            return $this->processResponse($response);
        } catch (\Exception $e) {
            Log::error('Erreur requête FORM:', [
                'message' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Erreur requête form: ' . $e->getMessage()
            ];
        }
    }

    private function processResponse($response)
    {
        $isJson = $this->isValidJson($response->body());

        if ($isJson) {
            $data = $response->json();
            return $data ?: [
                'success' => false,
                'message' => 'Réponse JSON vide ou invalide'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Réponse non-JSON reçue: ' . substr($response->body(), 0, 100) . '...',
                'raw_response' => $response->body()
            ];
        }
    }

    private function normalizeResponse($response)
    {
        if (!$response) {
            Log::warning('Réponse vide reçue de PayTech.');
            return [
                'success' => false,
                'message' => 'Réponse vide',
                'data' => []
            ];
        }

        if (isset($response['success']) && $response['success'] === true) {
            if (isset($response['data']) && !is_array($response['data'])) {
                if (is_string($response['data'])) {
                    $decodedData = json_decode($response['data'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $response['data'] = $decodedData;
                    } else {
                        $response['data'] = [
                            'token' => $response['data'],
                            'redirect_url' => $response['redirect_url'] ?? null
                        ];
                    }
                }
            }

            if (!isset($response['data']) || empty($response['data'])) {
                $response['data'] = [];

                if (isset($response['token'])) {
                    $response['data']['token'] = $response['token'];
                }

                if (isset($response['redirect_url'])) {
                    $response['data']['redirect_url'] = $response['redirect_url'];
                }
            }
        }

        Log::info('Réponse normalisée finale PayTech:', [
            'réponse' => $response
        ]);

        return $response;
    }

    private function isValidJson($string)
    {
        if (!is_string($string)) return false;

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function testConnectivity()
    {
        try {
            $result = Http::withOptions([
                'timeout' => 5,
            ])->get($this->baseUrl);

            if ($result->failed()) {
                Log::warning('Test de connectivité PayTech échoué: ' . $result->status());
            }
        } catch (\Exception $e) {
            Log::warning('Test de connectivité PayTech impossible: ' . $e->getMessage());
        }
    }

    private function validatePaymentData($data)
    {

        if (!isset($data['ipn_url'])) {
            $data['ipn_url'] = config('paytech.ipn_url');
        }
        
        $requiredFields = ['item_name', 'item_price', 'ref_command', 'success_url', 'ipn_url', 'cancel_url'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Champ requis manquant: {$field}");
            }
        }

        if (!is_numeric($data['item_price'])) {
            throw new \InvalidArgumentException("Le prix doit être un nombre");
        }
        return $data; 

    }

    public function checkPaymentStatus($token)
    {
        try {
            $endpoint = "{$this->baseUrl}/payment/check-status";

            $response = Http::withOptions([
                'verify' => $this->shouldVerifySsl(),
                'timeout' => $this->timeout,
            ])->withHeaders([
                'API_KEY' => $this->apiKey,
                'API_SECRET' => $this->apiSecret,
            ])->get($endpoint, [
                'token' => $token
            ]);

            Log::info('Check statut PayTech:', [
                'token' => $token,
                'response' => $response->body()
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'message' => 'Échec de la vérification: ' . $response->body()
                ];
            }

            return $response->json() ?: [
                'success' => false,
                'message' => 'Réponse invalide'
            ];
        } catch (\Exception $e) {
            Log::error('Erreur vérification statut:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    private function validateUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception("URL invalide: $url");
        }
        return $this->ensureHttps($url);
    }

    private function ensureHttps($url)
    {
        return str_replace('http://', 'https://', $url);
    }

    protected function shouldVerifySsl()
    {
        if (app()->environment('production')) {
            return true;
        }

        $certPath = storage_path('certs/cacert.pem');
        if (file_exists($certPath)) {
            return $certPath;
        }

        return app()->environment('local') ? false : true;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commnande;
use App\Models\Zone;
use App\Models\Tarif;
use App\Services\PaytechService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommnandeController extends Controller
{
    // Définition des suppléments pour les types de livraison
    const SUPPLEMENT_STANDARD = 500;  // FCFA
    const SUPPLEMENT_EXPRESS = 1000;  // FCFA
    
    protected $paytechService;
    
    public function __construct(PaytechService $paytechService)
    {
        $this->paytechService = $paytechService;
    }
    
    public function create()
    {
        // Récupérer tous les tarifs
        $tarifs = Tarif::all();
        
        // Récupérer toutes les zones
        $zones = Zone::all();
        
        // Transmettre les suppléments à la vue pour le calcul JS
        $supplements = [
            'standard' => self::SUPPLEMENT_STANDARD,
            'express' => self::SUPPLEMENT_EXPRESS
        ];
        
        return view('commnandes.create', compact('zones', 'tarifs', 'supplements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'adresse_depart' => 'required|string|max:255',
            'adresse_arrivee' => 'required|string|max:255',
            'type_colis' => 'required|string',
            'type_livraison' => 'required|in:standard,express',
            'prix' => 'sometimes|numeric',
            'region_depart' => 'sometimes|string',
            'region_arrivee' => 'sometimes|string',
            'type_zone' => 'sometimes|string',
            'mode_paiement' => 'required|string|in:wave,orange money,tokens'
        ]);

        // Si les régions sont déjà dans la requête, les utiliser
        if ($request->filled('region_depart') && $request->filled('region_arrivee')) {
            $regionDepart = $request->region_depart;
            $regionArrivee = $request->region_arrivee;
        } else {
            // Sinon, extraire les régions des adresses
            $regionDepart = $this->extraireRegion($validated['adresse_depart']);
            $regionArrivee = $this->extraireRegion($validated['adresse_arrivee']);
        }

        if (!$regionDepart || !$regionArrivee) {
            return redirect()->back()
                ->with('error', 'Impossible de déterminer les régions à partir des adresses fournies.')
                ->withInput();
        }

        // Si un prix est déjà calculé côté client et transmis, l'utiliser
        if ($request->filled('prix')) {
            $prixBase = $request->prix;
        } else {
            // Sinon, rechercher le tarif correspondant sans tenir compte du type de livraison
            $tarif = $this->findTarifSansLivraison($regionDepart, $regionArrivee, $validated['type_colis']);

            if (!$tarif) {
                return redirect()->back()
                    ->with('error', 'Aucun tarif disponible pour cette combinaison.')
                    ->withInput();
            }
            
            $prixBase = $tarif->prix;
        }

        // Appliquer le supplément selon le type de livraison
        $prixFinal = $this->calculerPrixAvecSupplement($prixBase, $validated['type_livraison']);

        // Générer une référence unique pour la commande
        $reference = 'CMD-' . strtoupper(Str::random(8));
        
        // Créer la commande en attente de paiement
        $commande = new Commnande();
        $commande->reference = $reference;
        $commande->adresse_depart = $validated['adresse_depart'];
        $commande->adresse_arrivee = $validated['adresse_arrivee'];
        $commande->region_depart = $regionDepart;
        $commande->region_arrivee = $regionArrivee;
        $commande->type_zone = $request->type_zone ?? null;
        $commande->type_colis = $validated['type_colis'];
        $commande->type_livraison = $validated['type_livraison'];
        $commande->prix_base = $prixBase;
        $commande->prix_final = $prixFinal;
        $commande->status = 'en_attente_paiement';
        $commande->mode_paiement = $validated['mode_paiement'];
        $commande->user_id = Auth::id();
        $commande->save();

        // Si paiement par jetons, traiter directement
        // if ($validated['payment'] === 'tokens') {
        //     return $this->processTokenPayment($commande);
        // }
        
        // Sinon, rediriger vers PayTech
        return $this->redirectToPaytech($commande);
    }
    
    /**
     * Rediriger vers PayTech pour le paiement
     */
//  

/**
 * Rediriger vers PayTech pour le paiement
 */
// private function redirectToPaytech(Commnande $commande)
// {
//     // Vérifier si l'environnement est en local, forcer HTTPS pour les URLs
//     $successUrl = app()->environment('local') ? secure_url(route('commnandes.payment.success')) : route('commnandes.payment.success');
//     $ipnUrl = app()->environment('local') ? secure_url(route('commnandes.payment.ipn')) : route('commnandes.payment.ipn');
//     $cancelUrl = app()->environment('local') ? secure_url(route('commnandes.payment.cancel')) : route('commnandes.payment.cancel');

//     // Construction des données pour la requête de paiement
//     $paymentData = [
//         'item_name' => 'Livraison ' . $commande->reference,
//         'item_price' => number_format($commande->prix_final, 2, '.', ''),
//         'ref_command' => $commande->reference,
//         'command_name' => 'Livraison ' . $commande->type_colis,
//         'success_url' => $successUrl,
//         'ipn_url' => $ipnUrl,
//         'cancel_url' => $cancelUrl,
//         'custom_field' => (object)[
//             'commande_id' => $commande->id,
//             'user_id' => $commande->user_id,
//             'mode_paiement' => $commande->mode_paiement
//         ]
//     ];

//     try {
//         // Appel à PayTech pour créer la requête de paiement
//         $response = $this->paytechService->createPaymentRequest($paymentData);

//         // Vérifier la réponse et rediriger l'utilisateur
//         if (isset($response['success']) && $response['success']) {
//             // Mettre à jour la commande avec les données de paiement
//             $commande->update([
//                 'payment_token' => $response['data']['token'] ?? null,
//                 'payment_data' => is_string($response['data']) ? $response['data'] : json_encode($response['data']),
//             ]);

//             // Rediriger l'utilisateur vers l'URL de paiement de PayTech
//             return redirect()->away($response['data']['redirect_url']);
//         }

//         // Si la réponse PayTech n'est pas valide, rediriger avec une erreur
//         return redirect()->route('commnandes.create')
//             ->with('error', 'Erreur PayTech: ' . ($response['message'] ?? 'Erreur inconnue'))
//             ->withInput();

//     } catch (\Exception $e) {
//         // Log des erreurs pour faciliter le débogage
//         Log::error('Erreur lors de la création de la requête PayTech:', [
//             'message' => $e->getMessage(),
//             'stack' => $e->getTraceAsString(),
//         ]);

//         // Rediriger avec un message d'erreur générique
//         return redirect()->route('commnandes.create')
//             ->with('error', 'Erreur interne: ' . $e->getMessage())
//             ->withInput();
//     }
// }
private function redirectToPaytech(Commnande $commande)
{
    // Toujours forcer HTTPS pour les URLs, quelle que soit l'environnement
    $baseUrl = config('app.url');
    // S'assurer que l'URL de base est en HTTPS
    $baseUrl = str_replace('http://', 'https://', $baseUrl);
    
    // Construire les URLs en forçant HTTPS
    $successUrl = $baseUrl . '/commnandes/payment/success';
    $ipnUrl = $baseUrl . '/commnandes/payment/ipn';
    $cancelUrl = $baseUrl . '/commnandes/payment/cancel';

    // Construction des données pour la requête de paiement
    $paymentData = [
        'item_name' => 'Livraison ' . $commande->reference,
        'item_price' => number_format($commande->prix_final, 2, '.', ''),
        'ref_command' => $commande->reference,
        'command_name' => 'Livraison ' . $commande->type_colis,
        'success_url' => $successUrl,
        'ipn_url' => $ipnUrl,
        'cancel_url' => $cancelUrl,
        'custom_field' => [  // Envoyez directement un tableau, le service se chargera de l'encoder
            'commande_id' => $commande->id,
            'user_id' => $commande->user_id,
            'mode_paiement' => $commande->mode_paiement
        ]
    ];

    // Log les données qui seront envoyées à PayTech
    Log::info('Données envoyées à PayTech:', [
        'paymentData' => $paymentData,
        'baseUrl' => $baseUrl
    ]);

    try {
        // Appel à PayTech pour créer la requête de paiement
        $response = $this->paytechService->createPaymentRequest($paymentData);

        // Log de la réponse pour débogage
        Log::info('Réponse complète PayTech:', $response);

        // Vérifier simplement si la réponse est un succès ET si redirect_url existe
        if (isset($response['success']) && $response['success'] && isset($response['data']['redirect_url'])) {
            // Mettre à jour la commande avec les données de paiement
            $commande->update([
                'payment_token' => $response['data']['token'] ?? null,
                'payment_data' => json_encode($response['data']),
            ]);

            // Rediriger l'utilisateur vers l'URL de paiement de PayTech
            return redirect()->away($response['data']['redirect_url']);
        } 
        // Si c'est une réponse de succès mais sans l'URL requise
        else if (isset($response['success']) && $response['success']) {
            // Plus de détails dans le log pour aider au débogage
            Log::error('Réponse PayTech sans URL de redirection:', [
                'fullResponse' => $response,
                'containsData' => isset($response['data']),
                'dataType' => isset($response['data']) ? gettype($response['data']) : 'non défini'
            ]);
            return $this->handlePaymentError('Réponse incomplète de PayTech (URL manquante)', $commande);
        }
        // Si c'est une erreur explicite
        else {
            $errorMessage = $response['message'] ?? 'Erreur inconnue';
            Log::error('Erreur PayTech explicite:', [
                'errorMessage' => $errorMessage,
                'fullResponse' => $response
            ]);
            return $this->handlePaymentError($errorMessage, $commande);
        }
    } catch (\Exception $e) {
        // Log des erreurs pour faciliter le débogage
        Log::error('Exception lors de la création de la requête PayTech:', [
            'message' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
        ]);

        return $this->handlePaymentError($e->getMessage(), $commande);
    }
}
/**
 * Traite une erreur de paiement de manière standardisée
 */
private function handlePaymentError($errorMessage, Commnande $commande)
{
    // Mettre à jour la commande pour indiquer l'erreur
    $commande->update([
        'status' => 'erreur_paiement',
        'erreur_details' => $errorMessage
    ]);

    // Rediriger avec un message d'erreur
    return redirect()->route('commnandes.create')
        ->with('error', 'Erreur de paiement: ' . $errorMessage)
        ->withInput();
}

private function formatCustomFields($fields)
{
    if (empty($fields)) {
        return (object)[]; // Objet vide standardisé
    }
    
    // Conversion profonde en objet si tableau multidimensionnel
    return is_array($fields) ? json_decode(json_encode($fields)) : $fields;
}
    
    /**
     * Traiter le paiement par jetons
     */
    // private function processTokenPayment(Commnande $commande)
    // {
    //     $user = Auth::user();
        
    //     // Vérifier si l'utilisateur a assez de jetons
    //     if ($user->tokens < $commande->prix_final) {
    //         return redirect()->route('commnandes.create')
    //             ->with('error', 'Solde de jetons insuffisant pour cette commande');
    //     }
        
    //     // Déduire les jetons
    //     $user->tokens -= $commande->prix_final;
    //     $user->save();
        
    //     // Mettre à jour la commande
    //     $commande->status = 'payee';
    //     $commande->date_paiement = now();
    //     $commande->save();
        
    //     return redirect()->route('commnandes.confirmation', $commande->id)
    //         ->with('success', 'Paiement par jetons effectué avec succès!');
    // }
    
    /**
     * Callback IPN (Instant Payment Notification) de PayTech
     */
    public function ipnCallback(Request $request)
    {
        Log::info('IPN PayTech reçu', $request->all());
        
        $token = $request->input('token');
        
        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'Token manquant'], 400);
        }
        
        // Vérifier le statut du paiement
        $statusResponse = $this->paytechService->checkPaymentStatus($token);
        
        if (!$statusResponse['success']) {
            Log::error('Erreur vérification statut PayTech', $statusResponse);
            return response()->json(['status' => 'error', 'message' => 'Erreur vérification statut'], 500);
        }
        
        $paymentStatus = $statusResponse['data']['payment_status'] ?? null;
        $commandRef = $statusResponse['data']['ref_command'] ?? null;
        
        if (!$commandRef) {
            return response()->json(['status' => 'error', 'message' => 'Référence commande manquante'], 400);
        }
        
        // Trouver la commande
        $commande = Commnande::where('reference', $commandRef)->first();
        
        if (!$commande) {
            return response()->json(['status' => 'error', 'message' => 'Commande non trouvée'], 404);
        }
        
        // Mettre à jour le statut de la commande
        if ($paymentStatus === 'completed') {
            $commande->status = 'payee';
            $commande->date_paiement = now();
            $commande->save();
            
            Log::info('Paiement complété pour la commande ' . $commande->reference);
        }
        
        return response()->json(['status' => 'success', 'message' => 'IPN traité']);
    }
    
    /**
     * Page de confirmation après paiement réussi
     */
    public function paymentSuccess(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return redirect()->route('commnandes.index')
                ->with('error', 'Information de paiement manquante');
        }
        
        // Vérifier le statut du paiement
        $statusResponse = $this->paytechService->checkPaymentStatus($token);
        
        if (!$statusResponse['success']) {
            return redirect()->route('commnandes.index')
                ->with('error', 'Erreur lors de la vérification du paiement');
        }
        
        $paymentStatus = $statusResponse['data']['payment_status'] ?? null;
        $commandRef = $statusResponse['data']['ref_command'] ?? null;
        
        // Trouver la commande
        $commande = Commnande::where('reference', $commandRef)->first();
        
        if (!$commande) {
            return redirect()->route('commnandes.index')
                ->with('error', 'Commande non trouvée');
        }
        
        // Si le paiement est réussi, rediriger vers la page de confirmation
        if ($paymentStatus === 'completed') {
            return redirect()->route('commnandes.confirmation', $commande->id)
                ->with('success', 'Paiement effectué avec succès!');
        }
        
        // Sinon, rediriger vers la liste des commandes avec un message
        return redirect()->route('commnandes.index')
            ->with('warning', 'Le statut de votre paiement est en attente de confirmation');
    }
    
    /**
     * Page d'annulation de paiement
     */
    public function paymentCancel(Request $request)
    {
        return redirect()->route('commnandes.index')
            ->with('error', 'Le paiement a été annulé');
    }
    
    /**
     * Page de confirmation après commande
     */
    public function confirmation($id)
    {
        $commande = Commnande::findOrFail($id);
        
        // Vérifier que l'utilisateur est le propriétaire de la commande
        if ($commande->user_id !== Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        return view('commnandes.confirmation', compact('commande'));
    }

    /**
     * Calcule le prix final en ajoutant le supplément selon le type de livraison.
     */
    private function calculerPrixAvecSupplement($prixBase, $typeLivraison)
    {
        if ($typeLivraison === 'express') {
            return $prixBase + self::SUPPLEMENT_EXPRESS;
        } else {
            return $prixBase + self::SUPPLEMENT_STANDARD;
        }
    }

    /**
     * Trouve un tarif sans tenir compte du type de livraison.
     */
    private function findTarifSansLivraison($regionDepart, $regionArrivee, $typeColis)
    {
        // Trouver d'abord la zone correspondante
        $zone = Zone::where('region_depart', $regionDepart)
                   ->where('region_arrivee', $regionArrivee)
                   ->first();
        
        if (!$zone) {
            return null;
        }
        
        // Rechercher le tarif correspondant (sans type_livraison)
        $tarif = Tarif::where(function($query) use ($zone, $regionDepart, $regionArrivee) {
                // Chercher par type_zone si disponible
                if (!empty($zone->type_zone)) {
                    $query->where('type_zone', $zone->type_zone)
                          ->orWhere('zone', $zone->type_zone);
                }
                // Ou chercher directement par région
                $query->orWhere('zone', $regionDepart)
                      ->orWhere('zone', $regionArrivee);
            })
            ->where('tranche_poids', $typeColis)
            ->first();
            
        return $tarif;
    }

    /**
     * Extrait la région à partir d'une adresse.
     */
    private function extraireRegion($adresse)
    {
        // Récupérer toutes les régions connues
        $regions = Zone::select('region_depart')
            ->union(Zone::select('region_arrivee'))
            ->pluck('region_depart')
            ->unique()
            ->toArray();

        // Normaliser l'adresse
        $adresseNorm = $this->normaliserTexte($adresse);

        // Parcourir les régions pour trouver une correspondance
        foreach ($regions as $region) {
            if (strpos($adresseNorm, $this->normaliserTexte($region)) !== false) {
                return $region;
            }
        }

        return null;
    }

    /**
     * Normalise le texte (retire les accents, met en minuscule).
     */
    private function normaliserTexte($texte)
    {
        // Mettre en minuscule
        $texte = strtolower($texte);

        // Retirer les accents
        $texte = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texte);

        return $texte;
    }

    public function index()
    {
        // Récupérer les commandes de l'utilisateur connecté
        $commnandes = Commnande::where('user_id', Auth::id())->latest()->get();

        return view('commnandes.index', compact('commnandes'));
    }

    public function diagnosticPaytech()
{
    // Restreindre aux environnements non-production
    if (app()->environment('production')) {
        abort(403, 'Non autorisé en production');
    }

    // Récupérer les informations de configuration
    $config = [
        'api_key_present' => !empty(config('paytech.api_key')),
        'api_secret_present' => !empty(config('paytech.api_secret')),
        'base_url' => config('paytech.base_url'),
        'currency' => config('paytech.currency'),
        'env' => config('paytech.env'),
        'app_env' => app()->environment(),
        'app_url' => config('app.url'),
    ];

    // Données de test minimales
    $testData = [
        'item_name' => 'Diagnostic PayTech',
        'item_price' => '10.00',
        'ref_command' => 'DIAG-' . uniqid(),
        'command_name' => 'Test Diagnostic',
        'success_url' => url('/diagnostic-success'),
        'ipn_url' => url('/diagnostic-ipn'),
        'cancel_url' => url('/diagnostic-cancel'),
        'custom_field' => [
            'test' => true,
            'timestamp' => time()
        ]
    ];

    $results = [];

    // Test 1: Vérification de la configuration
    $results['config_check'] = $this->checkConfig();

    // Test 2: Test de connectivité au serveur
    $results['connectivity'] = $this->testConnectivity(config('paytech.base_url'));

    // Test 3: Test simple avec méthode JSON
    $results['json_test'] = $this->testPaytechRequest($testData, 'json');

    // Test 4: Test avec méthode form-urlencoded
    $results['form_test'] = $this->testPaytechRequest($testData, 'form');

    return view('diagnostics.paytech', [
        'config' => $config,
        'results' => $results,
        'testData' => $testData,
    ]);
}

/**
 * Vérifie la configuration PayTech
 */
private function checkConfig()
{
    $issues = [];
    
    if (empty(config('paytech.api_key'))) {
        $issues[] = "Clé API manquante";
    }
    
    if (empty(config('paytech.api_secret'))) {
        $issues[] = "Secret API manquant";
    }
    
    if (empty(config('paytech.base_url'))) {
        $issues[] = "URL de base non configurée";
    }
    
    // Vérifier si l'URL est accessible
    if (!empty(config('paytech.base_url'))) {
        try {
            $result = Http::timeout(5)->get(config('paytech.base_url'));
            if ($result->failed()) {
                $issues[] = "L'URL de base ne répond pas: " . $result->status();
            }
        } catch (\Exception $e) {
            $issues[] = "Impossible de se connecter à l'URL de base: " . $e->getMessage();
        }
    }
    
    return [
        'success' => empty($issues),
        'issues' => $issues
    ];
}

/**
 * Teste la connectivité à une URL
 */
private function testConnectivity($url)
{
    try {
        $start = microtime(true);
        $response = Http::timeout(5)->get($url);
        $time = round((microtime(true) - $start) * 1000); // En millisecondes
        
        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'time_ms' => $time,
            'message' => $response->successful() ? 'Connexion réussie' : 'Échec de connexion'
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
}

/**
 * Teste une requête PayTech
 */
private function testPaytechRequest($data, $method = 'json')
{
    try {
        $start = microtime(true);
        
        if ($method === 'json') {
            $result = $this->makeJsonPaytechRequest($data);
        } else {
            $result = $this->makeFormPaytechRequest($data);
        }
        
        $time = round((microtime(true) - $start) * 1000); // En millisecondes
        
        return array_merge($result, [
            'time_ms' => $time
        ]);
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage()
        ];
    }
}

/**
 * Crée une requête JSON PayTech
 */
private function makeJsonPaytechRequest($data)
{
    $endpoint = config('paytech.base_url') . '/payment/request-payment';
    
    // Formater les données
    $payload = [
        'item_name' => $data['item_name'],
        'item_price' => $data['item_price'],
        'currency' => config('paytech.currency', 'XOF'),
        'ref_command' => $data['ref_command'],
        'command_name' => $data['command_name'],
        'env' => config('paytech.env', 'test'),
        'success_url' => $data['success_url'],
        'ipn_url' => $data['ipn_url'],
        'cancel_url' => $data['cancel_url'],
        'custom_field' => json_encode($data['custom_field'])
    ];
    
    $response = Http::withOptions([
        'verify' => app()->environment('local') ? false : true,
        'timeout' => 10,
    ])->withHeaders([
        'API_KEY' => config('paytech.api_key'),
        'API_SECRET' => config('paytech.api_secret'),
        'Content-Type' => 'application/json',
    ])->post($endpoint, $payload);
    
    return [
        'success' => $response->successful(),
        'status' => $response->status(),
        'response' => $response->json() ?: ['raw' => $response->body()]
    ];
}

/**
 * Crée une requête FORM PayTech
 */
private function makeFormPaytechRequest($data)
{
    $endpoint = config('paytech.base_url') . '/payment/request-payment';
    
    // Formater les données
    $payload = [
        'item_name' => $data['item_name'],
        'item_price' => $data['item_price'],
        'currency' => config('paytech.currency', 'XOF'),
        'ref_command' => $data['ref_command'],
        'command_name' => $data['command_name'],
        'env' => config('paytech.env', 'test'),
        'success_url' => $data['success_url'],
        'ipn_url' => $data['ipn_url'],
        'cancel_url' => $data['cancel_url'],
        'custom_field' => json_encode($data['custom_field'])
    ];
    
    $response = Http::withOptions([
        'verify' => app()->environment('local') ? false : true,
        'timeout' => 10,
    ])->withHeaders([
        'API_KEY' => config('paytech.api_key'),
        'API_SECRET' => config('paytech.api_secret'),
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->asForm()->post($endpoint, $payload);
    
    return [
        'success' => $response->successful(),
        'status' => $response->status(),
        'response' => $response->json() ?: ['raw' => $response->body()]
    ];
}
}
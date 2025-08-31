<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use App\Models\Commnande;
use App\Models\User;
use App\Models\Evaluation;
use App\Models\Zone;
use App\Models\DeliveryZone;
use App\Models\Tarif;
use App\Services\CinetPayService;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Notifications\SupportColisfast;

class CommnandeController extends Controller
{
    protected $cinetPayService;
    protected $geocodingService;

    public function __construct(CinetPayService $cinetPayService, GeocodingService $geocodingService)
    {
        $this->cinetPayService = $cinetPayService;
        $this->geocodingService = $geocodingService;
    }

 public function create()
{
    $tarifs = Tarif::all();
    $zones = Zone::all(); 
    $deliveryZones = DeliveryZone::all();
    $validTokens = [];
    $jetonPrice = null;
    $jetonZoneName = null;

    if (Auth::check()) {
        $user = Auth::user();
        $user->refresh();

        // Récupérer les jetons valides pour la zone Dakar
        $dakarZone = $deliveryZones->where('name', 'Dakar')->first();
        if ($dakarZone) {
            $validTokens = $user->getValidDakarTokens();
            $jetonPrice = $dakarZone->base_token_price;
            $jetonZoneName = $dakarZone->name;
        }

        Log::info('Données utilisateur pour la vue create', [
            'user_id' => $user->user_id,
            'valid_tokens' => $validTokens,
            'token_balance' => $user->token_balance,
            'zone' => $dakarZone ? $dakarZone->toArray() : null,
            'zones' => $zones->toArray(), // Debug: vérifier les zones
        ]);
    }

    return view('commnandes.create', compact('tarifs', 'zones', 'deliveryZones', 'validTokens', 'jetonPrice', 'jetonZoneName'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'adresse_depart' => 'required|string|max:255',
        'adresse_arrivee' => 'required|string|max:255',
        'details_adresse_depart' => 'nullable|string|max:255',
        'details_adresse_arrivee' => 'nullable|string|max:255',
        'type_colis' => 'required|string|in:0-5 kg,5-20 kg,20-50 kg,50+ kg',
        'type_livraison' => 'required|in:standard,express',
        'prix' => 'sometimes|numeric|min:0',
        'region_depart' => 'required|string|max:255',
        'region_arrivee' => 'required|string|max:255',
        'type_zone' => 'sometimes|nullable|string|max:255',
        'numero_telephone' => 'required|string|max:20',
        'mode_paiement' => 'sometimes|in:tokens,payment',
        'delivery_zone_id' => 'nullable|numeric|exists:delivery_zones,id', // Changed to nullable
    ]);

    $user = Auth::user();
    if (!$user || !$user->user_id) {
        Log::error('Utilisateur non authentifié dans store', [
            'user' => $user ? $user->toArray() : null,
        ]);
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer une commande.');
    }

    Log::info('Utilisateur authentifié', [
        'user_id' => $user->user_id,
        'user_name' => $user->name,
    ]);

    DB::beginTransaction();

    try {
        Log::info('Requête reçue dans store', [
            'request' => $request->except(['_token']),
            'user_id' => $user->user_id,
            'delivery_zone_id' => $request->delivery_zone_id, // Debug: log delivery_zone_id
        ]);

        $regionDepart = $request->region_depart;
        $regionArrivee = $request->region_arrivee;
        $useTokens = $request->mode_paiement === 'tokens';

        // Vérifier que les régions sont dans Dakar pour les jetons
        $dakarZone = DeliveryZone::where('name', 'Dakar')->first();
        if (!$dakarZone) {
            throw new \Exception('Zone de livraison Dakar introuvable.');
        }

        if ($useTokens) {
            // Vérification simplifiée : seules les livraisons dans Dakar sont valides
            $normalizedDepart = $this->normaliserTexte($regionDepart);
            $normalizedArrivee = $this->normaliserTexte($regionArrivee);
            $normalizedDakar = $this->normaliserTexte('Dakar');

            if (strpos($normalizedDepart, $normalizedDakar) === false || strpos($normalizedArrivee, $normalizedDakar) === false) {
                Log::error('Validation des régions échouée pour tokens', [
                    'region_depart' => $regionDepart,
                    'region_arrivee' => $regionArrivee,
                ]);
                throw new \Exception('Les jetons ne sont valides que pour les livraisons dans Dakar.');
            }

            // Vérifier que delivery_zone_id est fourni pour les jetons
            if (!$request->delivery_zone_id || $request->delivery_zone_id != $dakarZone->id) {
                Log::error('delivery_zone_id invalide pour tokens', [
                    'delivery_zone_id' => $request->delivery_zone_id,
                    'expected_id' => $dakarZone->id,
                ]);
                throw new \Exception('ID de zone de livraison invalide pour les jetons.');
            }
        }

        // Déterminer delivery_zone_id
        $zoneId = $useTokens ? $dakarZone->id : null; 

        Log::info('Zone de livraison déterminée', [
            'zone_id' => $zoneId,
            'zone_name' => $dakarZone->name,
        ]);

        // Calcul du prix
        $prixBase = null;
        if ($request->filled('prix')) {
            $prixBase = $request->prix;
        } else {
            $tarif = $this->findTarifSansLivraison($regionDepart, $regionArrivee, $validated['type_colis']);
            $prixBase = $tarif ? $tarif->prix : null;
        }

        if (!$prixBase) {
            throw new \Exception('Aucun tarif disponible pour cette combinaison.');
        }

        $tokensToDebit = 1;
        $reference = 'CMD-' . strtoupper(Str::random(8));
        $typeZone = $request->type_zone ?? ($useTokens ? 'Dakar' : null);

        if ($useTokens) {
            Log::info('Tentative d\'utilisation des jetons', [
                'user_id' => $user->user_id,
                'zone_id' => $zoneId,
                'tokens_needed' => $tokensToDebit,
            ]);

            $validTokens = $user->getValidTokensForZone($zoneId);
            Log::info('Jetons disponibles vérifiés', [
                'user_id' => $user->user_id,
                'zone_id' => $zoneId,
                'zone_name' => $dakarZone->name,
                'valid_tokens' => $validTokens,
                'tokens_needed' => $tokensToDebit,
            ]);

            if ($validTokens >= $tokensToDebit) {
                $commande = Commnande::create([
                    'reference' => $reference,
                    'adresse_depart' => $validated['adresse_depart'],
                    'details_adresse_depart' => $validated['details_adresse_depart'] ?? null,
                    'adresse_arrivee' => $validated['adresse_arrivee'],
                    'details_adresse_arrivee' => $validated['details_adresse_arrivee'] ?? null,
                    'region_depart' => $regionDepart,
                    'region_arrivee' => $regionArrivee,
                    'type_zone' => $typeZone,
                    'type_colis' => $validated['type_colis'],
                    'type_livraison' => $validated['type_livraison'],
                    'prix_base' => $prixBase,
                    'prix_final' => $prixBase,
                    'numero_telephone' => $validated['numero_telephone'],
                    'status' => Commnande::STATUT_PAYEE,
                    'user_id' => $user->user_id,
                    'quantite' => $request->quantite ?? 1,
                    'delivery_zone_id' => $zoneId,
                ]);

                Log::info('Commande créée', [
                    'commande_id' => $commande->id,
                    'reference' => $reference,
                    'user_id' => $user->user_id,
                ]);

                // Débiter les jetons
                $tokenTransaction = $user->debitTokensForDelivery($zoneId, $tokensToDebit, $reference, $commande->id);
                Log::info('Jetons débités avec succès', [
                    'transaction_id' => $tokenTransaction->id,
                    'commande_id' => $commande->id,
                ]);

                DB::commit();
                $user->refresh();

                return redirect()->route('commnandes.show', $commande->id)
                    ->with('success', "Commande créée avec succès ! 1 jeton utilisé pour votre livraison à Dakar.");
            } else {
       throw new \Exception("Vous n'avez que $validTokens jeton(s) disponible(s) pour Dakar (certains peuvent être expirés), il vous en faut $tokensToDebit.");
            }
        }

        // Paiement en ligne
        $commande = Commnande::create([
            'reference' => $reference,
            'adresse_depart' => $validated['adresse_depart'],
            'details_adresse_depart' => $validated['details_adresse_depart'] ?? null,
            'adresse_arrivee' => $validated['adresse_arrivee'],
            'details_adresse_arrivee' => $validated['details_adresse_arrivee'] ?? null,
            'region_depart' => $regionDepart,
            'region_arrivee' => $regionArrivee,
            'type_zone' => $typeZone,
            'type_colis' => $validated['type_colis'],
            'type_livraison' => $validated['type_livraison'],
            'prix_base' => $prixBase,
            'prix_final' => $prixBase,
            'numero_telephone' => $validated['numero_telephone'],
            'status' => Commnande::STATUT_EN_ATTENTE,
            'user_id' => $user->user_id,
            'quantite' => $request->quantite ?? 1,
            'delivery_zone_id' => $zoneId, // Will be null for online payment
        ]);

        Log::info('Commande créée pour paiement en ligne', [
            'commande_id' => $commande->id,
            'reference' => $reference,
        ]);

        DB::commit();
        return $this->redirectToCinetPay($commande, $validated);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur création commande', [
            'error' => $e->getMessage(),
            'user_id' => $user->user_id ?? 'non_authentifie',
            'request_data' => $request->except(['_token']),
        ]);

        return redirect()->back()
            ->with('error', 'Erreur lors de la création de la commande : ' . $e->getMessage())
            ->withInput();
    }
}

    private function redirectToCinetPay(Commnande $commande, array $validated)
    {
        try {
            $successUrl = rtrim(config('app.url'), '/') . route('commnandes.payment.success', [], false);
            $ipnUrl = rtrim(config('app.url'), '/') . route('commnandes.payment.ipn', [], false);
            $cancelUrl = rtrim(config('app.url'), '/') . route('commnandes.payment.cancel', [], false);

            if (!filter_var($successUrl, FILTER_VALIDATE_URL) ||
                !filter_var($ipnUrl, FILTER_VALIDATE_URL) ||
                !filter_var($cancelUrl, FILTER_VALIDATE_URL)) {
                throw new \Exception('Invalid URL format for CinetPay');
            }

            $paymentData = [
                'item_name' => "Livraison {$commande->reference}",
                'item_price' => $commande->prix_final,
                'ref_command' => $commande->reference,
                'success_url' => $successUrl,
                'ipn_url' => $ipnUrl,
                'cancel_url' => $cancelUrl,
                'custom_field' => [
                    'commande_id' => $commande->id,
                    'user_id' => Auth::id(),
                ],
                'customer_name' => $validated['customer_name'] ?? Auth::user()->name,
                'customer_surname' => $validated['customer_surname'] ?? '',
                'customer_email' => $validated['customer_email'] ?? Auth::user()->email,
                'customer_phone' => $validated['customer_phone'] ?? $validated['numero_telephone'],
                'customer_address' => $validated['adresse_depart'],
                'customer_city' => $validated['region_depart'] ?? 'Dakar',
                'customer_country' => 'SN',
                'customer_state' => '',
                'customer_zip' => '',
            ];

            Log::info('CinetPay payment request data', $paymentData);

            $response = $this->cinetPayService->createPaymentRequest($paymentData);

            if (!$response['success']) {
                throw new \Exception($response['message']);
            }

            Log::info('CinetPay payment initiated', [
                'commande_id' => $commande->id,
                'transaction_id' => $response['data']['transaction_id'],
                'payment_url' => $response['data']['redirect_url'],
            ]);

            return redirect($response['data']['redirect_url']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la redirection vers CinetPay', [
                'error' => $e->getMessage(),
                'commande_id' => $commande->id,
            ]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la redirection vers le paiement : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function ipnCallback(Request $request)
    {
        Log::info('IPN CinetPay reçu', $request->all());
        
        if (!app()->environment('local', 'testing')) {
            if (!$this->cinetPayService->verifySignature($request->all())) {
                Log::error('Signature IPN CinetPay invalide');
                return response()->json(['status' => 'error', 'message' => 'Signature invalide'], 400);
            }
        }

        $transactionId = $request->input('cpm_trans_id');
        
        if (!$transactionId) {
            return response()->json(['status' => 'error', 'message' => 'Transaction ID manquant'], 400);
        }
        
        $statusResponse = $this->cinetPayService->checkPaymentStatus($transactionId);
        
        if (!$statusResponse['success']) {
            Log::error('Erreur vérification statut CinetPay', $statusResponse);
            return response()->json(['status' => 'error', 'message' => 'Erreur vérification statut'], 500);
        }
        
        $paymentStatus = $statusResponse['data']['payment_status'] ?? null;
        
        $metadata = $request->input('cpm_custom') ?? $statusResponse['data']['custom'] ?? '{}';
        
        $customData = json_decode($metadata, true);
        $commandeId = $customData['commande_id'] ?? null;
        
        if (!$commandeId) {
            return response()->json(['status' => 'error', 'message' => 'ID commande manquant'], 400);
        }
        
        $commande = Commnande::find($commandeId);
        
        if (!$commande) {
            return response()->json(['status' => 'error', 'message' => 'Commande non trouvée'], 404);
        }
        
        if ($paymentStatus === 'completed') {
            $commande->status = 'payee';
            $commande->date_paiement = now();
            $commande->save();
            
            Log::info('Paiement complété pour la commande ' . $commande->reference);
        }
        
        return response()->json(['status' => 'success', 'message' => 'IPN traité']);
    }
    
    public function paymentSuccess(Request $request)
    {
        $transactionId = $request->query('transaction_id') ?? $request->query('token');
        
        if (!$transactionId) {
            return redirect()->route('commnandes.index')
                ->with('error', 'Information de paiement manquante');
        }
        
        $statusResponse = $this->cinetPayService->checkPaymentStatus($transactionId);
        
        if (!$statusResponse['success']) {
            return redirect()->route('commnandes.index')
                ->with('error', 'Erreur lors de la vérification du paiement');
        }
        
        $paymentStatus = $statusResponse['data']['payment_status'] ?? null;
        
        $customData = json_decode($statusResponse['data']['custom'] ?? '{}', true);
        $commandeId = $customData['commande_id'] ?? null;
        
        if (!$commandeId) {
            return redirect()->route('commnandes.index')
                ->with('error', 'Commande non trouvée');
        }
        
        $commande = Commnande::find($commandeId);
        
        if (!$commande) {
            return redirect()->route('commnandes.index')
                ->with('error', 'Commande non trouvée');
        }
        
        if ($paymentStatus === 'completed') {
            return redirect()->route('commnandes.confirmation', $commande->id)
                ->with('success', 'Paiement effectué avec succès!');
        }
        
        return redirect()->route('commnandes.index')
            ->with('warning', 'Le statut de votre paiement est en attente de confirmation');
    }

    public function paymentCancel(Request $request)
    {
        $transactionId = $request->query('transaction_id') ?? $request->query('token');
        
        if ($transactionId) {
            $statusResponse = $this->cinetPayService->checkPaymentStatus($transactionId);
            if ($statusResponse['success']) {
                $customData = json_decode($statusResponse['data']['custom'] ?? '{}', true);
                $commandeId = $customData['commande_id'] ?? null;
                
                if ($commandeId) {
                    Log::info("Paiement annulé pour la commande $commandeId");
                }
            }
        }
        
        return redirect()->route('commnandes.index')
            ->with('error', 'Le paiement a été annulé');
    }

    public function diagnosticCinetPay()
    {
        if (app()->environment('production')) {
            abort(403, 'Non autorisé en production');
        }

        $config = [
            'site_id_present' => !empty(config('cinetpay.site_id')),
            'api_key_present' => !empty(config('cinetpay.api_key')),
            'secret_key_present' => !empty(config('cinetpay.secret_key')),
            'base_url' => config('cinetpay.base_url'),
            'currency' => config('cinetpay.currency'),
            'env' => config('cinetpay.env'),
            'app_env' => app()->environment(),
            'app_url' => config('app.url'),
        ];

        $testData = [
            'item_name' => 'Diagnostic CinetPay',
            'item_price' => '10.00',
            'ref_command' => 'DIAG-' . uniqid(),
            'command_name' => 'Test Diagnostic',
            'success_url' => url('/diagnostic-success'),
            'ipn_url' => url('/diagnostic-ipn'),
            'cancel_url' => url('/diagnostic-cancel'),
            'custom_field' => [
            'test' => true,
            'timestamp' => time()
            ],
            'customer_name' => 'Test',
            'customer_surname' => 'User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '770000000'
        ];

        $results = [];

        $results['config_check'] = $this->checkCinetPayConfig();

        $results['connectivity'] = $this->testConnectivity(config('cinetpay.base_url'));

        $results['json_test'] = $this->testCinetPayRequest($testData, 'json');

        return view('diagnostics.cinetpay', [
            'config' => $config,
            'results' => $results,
            'testData' => $testData,
        ]);
    }

    private function checkCinetPayConfig()
    {
        $issues = [];
        
        if (empty(config('cinetpay.site_id'))) {
            $issues[] = "Site ID manquant";
        }
        
        if (empty(config('cinetpay.api_key'))) {
            $issues[] = "Clé API manquante";
        }
        
        if (empty(config('cinetpay.secret_key'))) {
            $issues[] = "Secret API manquant";
        }
        
        if (empty(config('cinetpay.base_url'))) {
            $issues[] = "URL de base non configurée";
        }
        
        if (!empty(config('cinetpay.base_url'))) {
            try {
                $result = Http::timeout(5)->get(config('cinetpay.base_url'));
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

    private function testCinetPayRequest($data, $method = 'json')
    {
        try {
            $start = microtime(true);
            
            $result = $this->makeJsonCinetPayRequest($data);
            
            $time = round((microtime(true) - $start) * 1000);
            
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

    private function makeJsonCinetPayRequest($data)
    {
        $endpoint = config('cinetpay.base_url') . '/payment/request-payment';
        
        $payload = [
            'amount' => $data['item_price'],
            'currency' => config('cinetpay.currency', 'XOF'),
            'site_id' => config('cinetpay.site_id'),
            'transaction_id' => $data['ref_command'],
            'description' => $data['command_name'],
            'return_url' => $data['success_url'],
            'notify_url' => $data['ipn_url'],
            'customer_name' => $data['customer_name'],
            'customer_surname' => $data['customer_surname'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'metadata' => json_encode($data['custom_field'])
        ];
        
        $response = Http::withOptions([
            'verify' => app()->environment('local') ? false : true,
            'timeout' => 10,
        ])->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('cinetpay.api_key')
        ])->post($endpoint, $payload);
        
        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'response' => $response->json() ?: ['raw' => $response->body()]
        ];
    }

    private function testConnectivity($url)
    {
        try {
            $start = microtime(true);
            $response = Http::timeout(5)->get($url);
            $time = round((microtime(true) - $start) * 1000); 
            
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

    
public function findTarifSansLivraison($regionDepart, $regionArrivee, $typeColis)
{
    $zone = Zone::where('region_depart', $regionDepart)
                ->where('region_arrivee', $regionArrivee)
                ->first();

    if (!$zone) {
        Log::error('Aucune zone trouvée', [
            'region_depart' => $regionDepart,
            'region_arrivee' => $regionArrivee,
        ]);
        return null;
    }

    $tarif = Tarif::where('type_zone', $zone->type_zone)
                  ->where('tranche_poids', $typeColis)
                  ->first();

    Log::info('Tarif recherché', [
        'type_zone' => $zone->type_zone,
        'tranche_poids' => $typeColis,
        'tarif_trouve' => $tarif ? $tarif->prix : 'non trouvé',
    ]);

    return $tarif;
}
    private function normaliserTexte($texte)
    {
        $texte = strtolower($texte);
        $texte = iconv('UTF-8', 'ASCII//TRANSLIT', $texte);
        $texte = preg_replace('/[^a-z0-9\s]/', '', $texte);
        return trim($texte);
    }
    
   private function extraireRegion($adresse)
    {
        // Simplification : retourner "Dakar" si l'adresse contient une référence à Dakar
        $normalizedAdresse = $this->normaliserTexte($adresse);
        $normalizedDakar = $this->normaliserTexte('Dakar');

        if (strpos($normalizedAdresse, $normalizedDakar) !== false) {
            return 'Dakar';
        }

        return null;
    }


public function index()
{
    $commandeEnCours = Commnande::where('user_id', Auth::id())
        ->whereIn('status', ['en_attente_paiement', 'payee', 'confirmee', 'acceptee', 'en_cours'])
        ->with('driver')
        ->latest()
        ->first();

    $livreursDisponibles = User::where('role', 'livreur')->get();
    
    $statistiques = [
        'total_commandes' => Commnande::where('user_id', Auth::id())->count(),
        'note_moyenne' => Evaluation::where('user_id', Auth::id())->avg('note'),
        'montant_total' => Commnande::where('user_id', Auth::id())->sum('prix_final'),
    ];

    // ✅ CORRECTION MAJEURE : Récupérer les livraisons avec les bonnes relations
    $livraisonsTerminees = Commnande::where('user_id', Auth::id())
        ->where('status', 'livree') 
        ->whereNotNull('driver_id') 
        ->with(['driver', 'evaluation']) 
        ->orderBy('updated_at', 'desc')
        ->get();

    \Log::info('Livraisons terminées récupérées:', [
        'count' => $livraisonsTerminees->count(),
        'commandes' => $livraisonsTerminees->pluck('id', 'status')->toArray()
    ]);

    return view('client.dashboard', compact('commandeEnCours', 'livreursDisponibles', 'statistiques', 'livraisonsTerminees'));
}
    
    public function show(Commnande $commnande)
    {
        if ($commnande->user_id !== auth()->id()) {
            abort(403);
        }

        return view('commnandes.show', compact('commnande'));
    }


    public function historique()
{
    $commnandes = Commnande::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();

    return view('commnandes.index', compact('commnandes'));
}

    public function confirmation($id)
    {
        $commande = Commnande::findOrFail($id);
        
        if ($commande->user_id !== Auth::id()) {
            abort(403, 'Non autorisé');
        }
        
        return view('commnandes.confirmation', compact('commande'));
    }

    

    public function confirm($id)
{
    $commnande = Commnande::findOrFail($id);

    if (in_array($commnande->status, [Commnande::STATUT_PAYEE, Commnande::STATUT_EN_ATTENTE])) {
        $commnande->status = Commnande::STATUT_CONFIRMEE;
        $commnande->save();

        return response()->json([
            'success' => true,
            'message' => 'Commande confirmée avec succès '
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Impossible de confirmer cette commande.'
    ]);
}


    public function indexLivreur()
    {
        $commandes = Commnande::where('driver_id', Auth::id())
                      ->whereIn('status', [Commnande::STATUT_ACCEPTEE, Commnande::STATUT_EN_COURS])
                     ->latest()
                     ->get();

        return view('livreur.commandes.index', compact('commandes'));
    }
}
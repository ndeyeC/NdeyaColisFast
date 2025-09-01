<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use App\Models\Commnande;
use App\Models\User;
use App\Models\Evaluation;
use App\Models\Zone;
use App\Services\PayDunyaService;
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
              protected $payDunyaService;
                protected $geocodingService;

    public function __construct(PayDunyaService $payDunyaService, GeocodingService $geocodingService) // Changement ici
    {
        $this->payDunyaService = $payDunyaService; // Changement ici
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
       return $this->redirectToPayDunya($commande, $validated);

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

  private function redirectToPayDunya(Commnande $commande, array $validated)
{
    try {
        $successUrl = rtrim(config('app.url'), '/') . route('commnandes.payment.success', [], false);
        $ipnUrl = rtrim(config('app.url'), '/') . route('commnandes.payment.ipn', [], false);
        $cancelUrl = rtrim(config('app.url'), '/') . route('commnandes.payment.cancel', [], false);

        // VALIDATION CORRIGÉE - Autoriser localhost en développement
        $urls = [$successUrl, $ipnUrl, $cancelUrl];
        foreach ($urls as $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                Log::error('URL de callback invalide', ['url' => $url]);
                throw new \Exception('URL de callback invalide : ' . $url);
            }
            
            // Bloquer localhost SEULEMENT en production
            if (app()->environment('production') && strpos($url, 'localhost') !== false) {
                Log::error('URL localhost non autorisée en production', ['url' => $url]);
                throw new \Exception('URL localhost non accessible publiquement en production : ' . $url);
            }
        }

        // Reste du code inchangé...
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
            'customer_email' => $validated['customer_email'] ?? Auth::user()->email,
            'customer_phone' => $validated['customer_phone'] ?? $validated['numero_telephone'],
        ];

        $response = $this->payDunyaService->createPaymentRequest($paymentData);

        if ($response['success'] && isset($response['data']['redirect_url'])) {
            return redirect($response['data']['redirect_url']);
        }

        throw new \Exception($response['message'] ?? 'Erreur lors de l\'initialisation du paiement');
        
    } catch (\Exception $e) {
        Log::error('Erreur PayDunya', [
            'message' => $e->getMessage(),
            'commande_id' => $commande->id,
        ]);
        return redirect()->back()
            ->with('error', 'Erreur lors de la redirection vers le paiement : ' . $e->getMessage())
            ->withInput();
    }
}


   public function ipnCallback(Request $request)
{
    Log::info('IPN PayDunya reçu', $request->all());

    $data = json_decode($request->input('data', '{}'), true);
    $token = $data['invoice']['token'] ?? null;

    if (!$token) {
        return response()->json(['status' => 'error', 'message' => 'Token manquant'], 400);
    }

    $statusResponse = $this->payDunyaService->checkPaymentStatus($token);

    if (!$statusResponse['success']) {
        Log::error('Erreur vérification statut PayDunya', $statusResponse);
        return response()->json(['status' => 'error', 'message' => 'Erreur vérification statut'], 500);
    }

    $paymentStatus = $statusResponse['data']['payment_status'] ?? null;

    // Utiliser transaction_id comme référence
    $customData = $statusResponse['data']['custom_data'] ?? [];
    $transactionId = $customData['transaction_id'] ?? null;

    if (!$transactionId) {
        return response()->json(['status' => 'error', 'message' => 'Transaction ID manquant'], 400);
    }

    $commande = Commnande::where('transaction_id', $transactionId)->first();

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
    $transactionId = $request->query('transaction_id');

    $commande = Commnande::where('transaction_id', $transactionId)->first();

    if (!$commande) {
        return redirect()->route('commnandes.index')
            ->with('error', 'Commande introuvable');
    }

    // Vérifier le paiement via PayDunya
    $statusResponse = $this->payDunyaService->checkPaymentStatus($commande->transaction_id);

    if ($statusResponse['success'] && $statusResponse['data']['payment_status'] === 'completed') {
        return redirect()->route('commnandes.confirmation', $commande->id)
            ->with('success', 'Paiement effectué avec succès !');
    }

    return redirect()->route('commnandes.index')
        ->with('warning', 'Le paiement est en attente de confirmation.');
}



    public function paymentCancel(Request $request)
    {
        $token = $request->query('token');
        
        if ($token) {
            $statusResponse = $this->payDunyaService->checkPaymentStatus($token);
            if ($statusResponse['success']) {
                $customData = $statusResponse['data']['custom_data'] ?? [];
                $commandeId = $customData['commande_id'] ?? null;
                
                if ($commandeId) {
                    Log::info("Paiement annulé pour la commande $commandeId");
                }
            }
        }
        
        return redirect()->route('commnandes.index')
            ->with('error', 'Le paiement a été annulé');
    }

    public function diagnosticPayDunya() // Nouvelle méthode de diagnostic
    {
        if (app()->environment('production')) {
            abort(403, 'Non autorisé en production');
        }

        $config = [
            'master_key_present' => !empty(config('paydunya.master_key')),
            'private_key_present' => !empty(config('paydunya.private_key')),
            'public_key_present' => !empty(config('paydunya.public_key')),
            'token_present' => !empty(config('paydunya.token')),
            'base_url' => config('paydunya.base_url'),
            'currency' => config('paydunya.currency'),
            'mode' => config('paydunya.mode'),
            'app_env' => app()->environment(),
            'app_url' => config('app.url'),
        ];

        $testData = [
            'item_name' => 'Diagnostic PayDunya',
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

        $results['config_check'] = $this->checkPayDunyaConfig();
        $results['connectivity'] = $this->testConnectivity(config('paydunya.base_url'));
        $results['api_test'] = $this->testPayDunyaRequest($testData);

        return view('diagnostics.paydunya', [
            'config' => $config,
            'results' => $results,
            'testData' => $testData,
        ]);
    }

    private function checkPayDunyaConfig()
    {
        $issues = [];
        
        if (empty(config('paydunya.master_key'))) {
            $issues[] = "Master Key manquante";
        }
        
        if (empty(config('paydunya.private_key'))) {
            $issues[] = "Clé privée manquante";
        }
        
        if (empty(config('paydunya.token'))) {
            $issues[] = "Token manquant";
        }
        
        if (empty(config('paydunya.base_url'))) {
            $issues[] = "URL de base non configurée";
        }
        
        return [
            'success' => empty($issues),
            'issues' => $issues
        ];
    }

    private function testPayDunyaRequest($data)
    {
        try {
            $start = microtime(true);
            
            $result = $this->payDunyaService->createPaymentRequest($data);
            
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
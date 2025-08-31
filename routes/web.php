<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\LivreurController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommnandeController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\LivraisonEnCoursController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\SuiviLivraisonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserMessagesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\RevenulivreurController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\Admin\TokenPriceController;
use App\Http\Controllers\PageController;







// Accueil
Route::get('/', fn () => view('welcome'))->name('welcome');

Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'livreur' => redirect()->route('livreur.dashboarde'), 
        'client' => redirect()->route('client.dashboard'),
        default => abort(403),
    };
})->middleware('auth')->name('dashboard');

// Authenticated profile actions
Route::middleware(['auth'])->group(function () {
   Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Redirections après login (gérées côté AuthController)

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/trajets-urbains', [DashboardController::class, 'trajetsUrbains'])->name('trajets.urbains');
    Route::get('/trajets/{trajet}/assigner', [DashboardController::class, 'showLivraisonsPourAssignation'])->name('trajets.assigner');
    Route::post('/trajets/{trajet}/assigner', [DashboardController::class, 'assignerLivraisons'])->name('trajets.assigner');


    // Livreurs
    Route::get('/livreurs', [LivreurController::class, 'index'])->name('livreurs.index');
    Route::get('/livreurs/{livreur}', [LivreurController::class, 'show'])->name('livreurs.show');
    Route::delete('/livreurs/{livreur}', [LivreurController::class, 'destroy'])->name('livreurs.destroy');
    Route::get('/livreurs/{id}/json', [LivreurController::class, 'showJson']);
    

    // Tarifs
    Route::resource('tarifs', TarifController::class);

    // Communications
    Route::get('/communications', [CommunicationController::class, 'index'])->name('communications.index');
    Route::post('/communications', [CommunicationController::class, 'store'])->name('communications.store');
    Route::get('/communications/conversation', [CommunicationController::class, 'conversation'])->name('communications.conversation');
    Route::get('/communications/user-conversation', [CommunicationController::class, 'getUserConversation'])->name('communications.user-conversation');
    Route::post('/communications/new', [CommunicationController::class, 'getNewMessages'])->name('communications.new');
    Route::get('/communications/unread-counts', [CommunicationController::class, 'getUnreadCounts'])->name('communications.unread-counts');
    Route::post('/communications/send-from-user', [CommunicationController::class, 'sendFromUser'])->name('communications.send-from-user');

    // Notifications
    Route::post('/notifications/mark-all-read', fn () => Auth::user()->notifications()->update(['is_read' => true]))->name('notifications.mark-all-read');
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::delete('/notifications/delete/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');

    // Livraisons admin
    Route::get('/livraisons', [SuiviLivraisonController::class, 'index'])->name('livraisons.index');
    Route::get('/livraisons/{id}', [SuiviLivraisonController::class, 'show'])->name('livraisons.show');
    Route::post('/livraisons/{id}/update-status', [SuiviLivraisonController::class, 'updateStatus'])->name('livraisons.update-status');
    Route::post('/livraisons/{id}/assign-driver', [SuiviLivraisonController::class, 'assignDriver'])->name('livraisons.assign-driver');
    Route::get('/livraisons/problemes/liste', [SuiviLivraisonController::class, 'problemesSignales'])->name('livraisons.problemes');
    Route::post('/livraisons/{id}/resoudre-probleme', [SuiviLivraisonController::class, 'resoudreProbleme'])->name('livraisons.resolve');
    Route::get('/livraisons/{id}/json', [SuiviLivraisonController::class, 'showJson'])->name('livraisons.json');
    Route::get('/livraisons/export', [SuiviLivraisonController::class, 'export'])->name('livraisons.export');
    Route::get('/api/livraisons/{id}/details', [SuiviLivraisonController::class, 'getDetails'])->name('livraisons.details');

    // Statistiques
    Route::get('/statistiques', fn () => view('admin.statistiques.index'))->name('statistiques.index');
});

Route::get('/livreur/trajets/{trajet}/commandes', [LivreurController::class, 'voirCommandesAssignes'])->name('livreur.trajets.commandes');

Route::middleware(['auth', 'role:livreur'])->prefix('livreur')->name('livreur.')->group(function () {
    Route::get('/dashboarde', [LivreurController::class, 'dashboarde'])->name('dashboarde');
    Route::get('/livraisons-disponible', [LivreurController::class, 'commandesDisponibles'])->name('livraisons-disponible');
    Route::post('/commandes/{id}/accepter', [LivreurController::class, 'accepterCommande'])->name('commandes.accepter');
    Route::post('/commandes/{id}/terminer', [LivreurController::class, 'terminerLivraison'])->name('commandes.terminer');
    Route::get('/mes-commandes', [LivreurController::class, 'mesCommandes'])->name('commandes.mes-commandes');
    Route::get('/commandes/{id}', [LivreurController::class, 'detailsCommande'])->name('commandes.details');
    Route::get('/statistiques', [LivreurController::class, 'statistiques'])->name('statistiques');
    
    
    Route::get('/livraison-cours', [LivraisonEnCoursController::class, 'index'])->name('livraison-cours');
   Route::post('/livraisons/{id}/demarrer', [LivraisonEnCoursController::class, 'demarrerLivraison'])->name('livraisons.demarrer');
    Route::post('/livraisons/{id}/marquer-livree', [LivraisonEnCoursController::class, 'marquerLivree'])->name('livraisons.marquer-livree');
    Route::post('/livraisons/{id}/signaler-probleme', [LivraisonEnCoursController::class, 'signalerProbleme'])->name('livraisons.signaler-probleme');
    Route::post('/livraisons/{id}/annuler', [LivraisonEnCoursController::class, 'annulerLivraison'])->name('livraisons.annuler');
    Route::get('/livraisons/{id}/navigation', [LivraisonEnCoursController::class, 'ouvrirNavigation'])->name('livraisons.navigation');
    Route::get('/revenus', [RevenuLivreurController::class, 'revenusView'])->name('revenus');
    Route::get('/api/revenus/{id}', [RevenuLivreurController::class, 'getGraphData'])
    ->name('api.revenus.graph');

     Route::get('/trajets', [LivreurController::class, 'listeTrajets'])->name('trajets.index');
    Route::get('/trajets/create', [LivreurController::class, 'createTrajet'])->name('trajets.create');
    Route::post('/trajets/store', [LivreurController::class, 'storeTrajet'])->name('trajets.store');


});
Route::post('/client/ajouter-livreur-favori', [App\Http\Controllers\ClientController::class, 'ajouterFavori'])
    ->name('client.ajouter-favori')
    ->middleware(['auth', 'role:client']);


// Client routes protégées
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/deliverers', fn () => view('client.deliverers'))->name('deliverers');
  Route::post('/evaluations', [EvaluationController::class, 'store'])
    ->name('evaluations');

    Route::get('/evaluations/create/{commande}', [EvaluationController::class, 'create'])->name('evaluations.create');


    Route::get('/aide', fn () => view('client.aide'))->name('aide');

});

// Routes partagées
Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('user.messages');
    Route::post('/messages/send', [MessageController::class, 'sendToAdmin'])->name('user.messages.send');
    Route::post('/messages/check-new', [MessageController::class, 'checkNewMessages'])->name('user.messages.check');

    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::post('/tokens/purchase', [TokenController::class, 'purchase'])->name('tokens.purchase');
    Route::get('/api/tokens/balance', [TokenController::class, 'getBalance']);
    Route::get('/statistiques', [StatistiquesController::class, 'index'])->name('statistiques.index');
    Route::post('/user/fcm-token', [UserController::class, 'saveFcmToken']);
});



Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/commnandes/create', [CommnandeController::class, 'create'])->name('commnandes.create');
    Route::post('/commnandes/{id}/confirm', [CommnandeController::class, 'confirm'])->name('commnandes.confirm');
    Route::post('/commnandes', [CommnandeController::class, 'store'])->name('commnandes.store');
    Route::get('/dashboard', [CommnandeController::class, 'index'])->name('dashboard');
    Route::get('/commnandes/confirmation/{id}', [CommnandeController::class, 'confirmation'])->name('commnandes.confirmation');
    Route::get('/commnandes/payment/success', [CommnandeController::class, 'paymentSuccess'])->name('commnandes.payment.success');
    Route::get('/commnandes/payment/cancel', [CommnandeController::class, 'paymentCancel'])->name('commnandes.payment.cancel');
    Route::match(['GET', 'POST'], '/commnandes/payment/ipn', [CommnandeController::class, 'ipnCallback'])->name('commnandes.payment.ipn');
Route::get('/commnandes/index', [CommnandeController::class, 'historique'])->name('commnandes.index');
    Route::get('/commnandes/{commnande}', [CommnandeController::class, 'show'])->name('commnandes.show');
});
// Divers
Route::get('/suggestions', [\App\Http\Controllers\SuggestionController::class, 'getSuggestedCities']);
Route::middleware(['auth', 'role:livreur'])->get('/paiements/par-mois', [RevenuLivreurController::class, 'filterPaiementsParMois'])->name('paiements.par.mois');

Route::middleware(['auth', 'role:admin'])->get('/test-sms', [SmsController::class, 'envoyerSms']);

// jetons
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/token-prices', [TokenPriceController::class, 'index'])->name('token-prices.index');
        Route::get('/token-prices/{id}/edit', [TokenPriceController::class, 'edit'])->name('token-prices.edit');
        Route::put('/token-prices/{id}', [TokenPriceController::class, 'update'])->name('token-prices.update');
    });

    // Achat jeton

Route::middleware(['auth', 'role:client'])->group(function () {
    // Gestion des tokens
    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::post('/tokens/purchase', [TokenController::class, 'purchase'])->name('tokens.purchase');
});

Route::post('/tokens/payment/ipn', [TokenController::class, 'paymentIpn'])->name('tokens.payment.ipn');
Route::get('/tokens/payment/success', [TokenController::class, 'paymentSuccess'])->name('tokens.payment.success');
Route::get('/tokens/payment/cancel', [TokenController::class, 'paymentCancel'])->name('tokens.payment.cancel');

Route::get('/services/livraison-express', [PageController::class, 'livraisonExpress'])->name('services.livraison-express');
Route::get('/services/livraison-programmee', [PageController::class, 'livraisonProgrammee'])->name('services.livraison-programmee');
Route::get('/services/solutions-ecommerce', [PageController::class, 'solutionsEcommerce'])->name('services.solutions-ecommerce');

// Routes pour l'entreprise
Route::get('/apropos', [PageController::class, 'apropos'])->name('apropos');
Route::get('/carrieres', [PageController::class, 'carrieres'])->name('carrieres');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');

// Routes pour les pages légales
Route::get('/legal/conditions', [PageController::class, 'conditions'])->name('legal.conditions');
Route::get('/legal/confidentialite', [PageController::class, 'confidentialite'])->name('legal.confidentialite');
Route::get('/legal/mentions', [PageController::class, 'mentions'])->name('legal.mentions');
Route::get('/legal/cookies', [PageController::class, 'cookies'])->name('legal.cookies');
Route::get('/sitemap', [PageController::class, 'sitemap'])->name('sitemap');


// Auth routes
require __DIR__.'/auth.php';

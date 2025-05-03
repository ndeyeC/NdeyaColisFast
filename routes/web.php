<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\LivreurController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommnandeController;


Route::get('/', function () {
    return view('welcome');
});
// Route pour les admin
Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/commnandes/confirmation/{id}', [CommnandeController::class, 'confirmation'])->name('commnandes.confirmation');
Route::get('/commnandes', [CommnandeController::class, 'liste'])->name('commnandes.liste');
Route::get('/debug-paytech', [CommnandeController::class, 'debugPaytech'])->middleware('auth');
    // Routes de paiement
    Route::get('/commnandes/payment/success', [CommnandeController::class, 'paymentSuccess'])->name('commnandes.payment.success');
    Route::get('/commnandes/payment/cancel', [CommnandeController::class, 'paymentCancel'])->name('commnandes.payment.cancel');

// Route IPN (ne nécessite pas d'authentification)
Route::post('/commnandes/payment/ipn', [CommnandeController::class, 'ipnCallback'])->name('commnandes.payment.ipn');

Route::get('/admin/livreurs', [LivreurController::class, 'index'])->name('admin.livreurs.index');
Route::get('/admin/livreurs/{livreur}', [LivreurController::class, 'show'])
     ->name('admin.livreurs.show');
 Route::get('/admin/livreurs/{id}/json', [LivreurController::class, 'showJson']);
     


     Route::delete('/admin/livreurs/{livreur}', [LivreurController::class, 'destroy'])->name('admin.livreurs.destroy');

// Route::get('/admin.tarifs.index', function () {
//     return view('admin.tarifs.index');
// })->name('admin.tarifs.index');


// Route pour les communications 
// Route::get('admin/communications', [CommunicationController::class, 'index'])->name('admin.communications.index');
// Route::post('/admin/communications', [CommunicationController::class, 'store'])->name('communications.store');

Route::get('/admin/tarifs', [TarifController::class, 'index'])->name('admin.tarifs.index');
Route::get('/admin/tarifs/create', [TarifController::class, 'create'])->name('admin.tarifs.create');
Route::post('/admin/tarifs', [TarifController::class, 'store'])->name('admin.tarifs.store');
Route::get('/admin/tarifs/{tarif}/edit', [TarifController::class, 'edit'])->name('admin.tarifs.edit');
Route::put('/admin/tarifs/{tarif}', [TarifController::class, 'update'])->name('admin.tarifs.update');
Route::delete('/admin/tarifs/{tarif}', [TarifController::class, 'destroy'])->name('admin.tarifs.destroy');

Route::get('/admin.livraisons.index', function () {
    return view('admin.livraisons.index');
})->name('admin.livraisons.index');

Route::get('/admin.statistiques.index', function () {
    return view('admin.statistiques.index');
})->name('admin.statistiques.index');


// routes/web.php ou api.php
// Route::get('/admin/livreurs/{id}/json', [LivreurController::class, 'showJson'])->name('livreurs.show.json');

// Route::get('/admin.communications.index', function () {
//     return view('admin.communications.index');
// })->name('admin.communications.index');

// Route::get('/admin/communications', [CommunicationController::class, 'index'])->name('admin.communications.index');
// Route::post('/admin/communications', [CommunicationController::class, 'store'])->name('admin.communications.store');
// Route::post('notifications/{id}/read', [CommunicationController::class, 'markAsRead'])->name('notifications.read');

// Communications routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/communications', [CommunicationController::class, 'index'])->name('communications.index');
    Route::post('/communications', [CommunicationController::class, 'store'])->name('communications.store');
    Route::get('/communications/conversation', [CommunicationController::class, 'conversation'])->name('communications.conversation');
    // Route::get('/communications/livraison', [CommunicationController::class, 'getLivraisonMessages'])->name('communications.livraison');
    // Route::get('/communications/new', [CommunicationController::class, 'getNewMessages'])->name('communications.new');
    
    // Notifications routes

    // Routes pour les notifications
Route::post('/notifications/mark-all-read', function() {
    Auth::user()->notifications()->update(['is_read' => true]);
    return response()->json(['success' => true]);
});

// Route::post('/admin/notifications/delete/{id}', 'Admin\NotificationController@delete')->name('admin.notifications.delete');
Route::delete('/notifications/delete/{id}', [NotificationController::class, 'delete'])
     ->name('notifications.delete');

Route::get('/commandes/create', [CommandeController::class, 'create'])->name('commandes.create');
Route::post('/commandes', [CommandeController::class, 'store'])->name('commandes.store');



// Route::delete('/notifications/{notification}', function(Notification $notification) {
//     $notification->delete();
//     return response()->json(['success' => true]);
// })->middleware('auth');
    // Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    // Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    // Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    // Route::get('/notifications/new', [NotificationController::class, 'getNewNotifications'])->name('notifications.new');
});

// web.php (routes)

Route::get('/communications/conversation', [CommunicationController::class, 'conversation'])
     ->name('admin.communications.conversation');
     
// Route::get('/messages', [CommunicationController::class, 'showMessages'])->name('client.messages');
// Route::post('/message/send', [CommunicationController::class, 'send'])->name('client.message.send');

///route pour depos candidature
Route::get('/livreur.devenir_livreur', function () {
    return view('livreur.devenir_livreur');
})->name('livreur.devenir_livreur');

// Route pour les livreus
Route::get('/dashboarde', function () {
    return view('livreur.dashboarde');
})->name('livreur.dashboarde');

Route::get('/livraisons-disponible', function () {
    return view('livreur.livraisons-disponible');
})->name('livreur.livraisons-disponible');

Route::get('/livraison-cours', function () {
    return view('livreur.livraison-cours');
})->name('livreur.livraison-cours');

Route::get('/livraison', function () {
    // Dans une version statique, l'ID sera ignore
    return view('livreur.details-livraison');
})->name('livreur.details-livraison');

Route::get('/navigation', function () {
    return view('livreur.navigation');
})->name('livreur.navigation');

// Profil du livreur
Route::get('/profil', function () {
    return view('livreur.profil');
})->name('livreur.profil');

// Statistiques de performance
Route::get('/statistiques', function () {
    return view('livreur.statistics');
})->name('livreur.statistics');

// Revenus et paiements
Route::get('/revenus', function () {
    return view('livreur.revenus');
})->name('livreur.revenus');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });
// Route pour les clients

Route::get('/client/dashboard', function () {
    return view('client.dashboard');
})->name('client.dashboard');

Route::get('/delivery/new', function () {
    return view('delivery.new');
})->name('delivery.new');
Route::get('/delivery/history', function () {
    return view('delivery.history');
})->name('delivery.history');

 Route::get('/client/deliverers', function () {
    return view('client.deliverers');
 })->name('client.deliverers');


//  Route::middleware('auth')->group(function() {
//     Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
//     Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
//     Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/user/messages', [CommunicationController::class, 'index'])->name('user.messages');
    Route::post('/user/messages/send', [CommunicationController::class, 'sendFromUser'])->name('user.messages.send');
    Route::post('/user/messages/new', [CommunicationController::class, 'getNewMessages'])->name('user.messages.new');

});

// Dans votre groupe de routes 'admin'
Route::prefix('admin')->name('admin.')->group(function () {
    // ... autres routes existantes
    
    // Nouvelle route pour les nouveaux messages
    Route::get('/communications/new', [CommunicationController::class, 'getNewMessages'])->name('communications.new');
    
    // Route pour les compteurs de messages non lus
    Route::get('/communications/unread-counts', [CommunicationController::class, 'getUnreadCounts'])->name('communications.unread-counts');
    
    // Routes pour les notifications
    // Route::post('/notifications/mark-read/{notification}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    // Route::post('/notifications/delete/{notification}', [NotificationController::class, 'destroy'])->name('notifications.delete');
    
    // ... autres routes existantes
});

Route::get('/commnandes/create', [CommnandeController::class, 'create'])->name('commnandes.create');
Route::post('/commnandes', [CommnandeController::class, 'store'])->name('commnandes.store');
Route::get('/commnandes', [CommnandeController::class, 'index'])->name('commnandes.index');

Route::prefix('admin')->name('admin.')->group(function () {
    // ... autres routes
    
    // Routes pour les notifications
    Route::prefix('notifications')->group(function() {
        Route::post('/mark-all-read', function() {
            Auth::user()->notifications()->update(['is_read' => true]);
            return response()->json(['success' => true]);
        })->name('notifications.mark-all-read');
        
        Route::post('/mark-read/{id}', [NotificationController::class, 'markAsRead'])
             ->name('notifications.mark-read');
             
        Route::post('/delete/{id}', [NotificationController::class, 'destroy'])
             ->name('notifications.delete');
    });

  
});




require __DIR__.'/auth.php';

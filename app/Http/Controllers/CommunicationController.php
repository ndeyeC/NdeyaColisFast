<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Communication;
use App\Models\Notification;
use App\Models\User;
use App\Models\Livreur;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller
{
    public function index()
    {
        // Get clients and livreurs
        $users = User::where('role', 'client')->get();
        $livreurs = Livreur::all(); // Or use User::where('role', 'livreur') if livreurs are in User table
        
        // Get all unread notifications for the logged-in admin
        $notifications = Notification::where('user_id', auth()->id())
                                   ->where('is_read', false)
                                   ->latest()
                                   ->get();
        
        return view('admin.communications.index', compact(
            'users',
            'livreurs',
            'notifications'
        ));
    }
    
    public function conversation(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:App\Models\User,App\Models\Livreur'
        ]);

        $communications = Communication::where(function($q) use ($request) {
                $q->where('sender_id', auth()->id())
                  ->where('sender_type', 'App\Models\User')
                  ->where('receiver_id', $request->receiver_id)
                  ->where('receiver_type', $request->receiver_type);
            })
            ->orWhere(function($q) use ($request) {
                $q->where('receiver_id', auth()->id())
                  ->where('receiver_type', 'App\Models\User')
                  ->where('sender_id', $request->receiver_id)
                  ->where('sender_type', $request->receiver_type);
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Mark messages as read
        Communication::where('receiver_id', auth()->id())
                    ->where('receiver_type', 'App\Models\User')
                    ->where('sender_id', $request->receiver_id)
                    ->where('sender_type', $request->receiver_type)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

        return response()->json(['communications' => $communications]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:App\Models\User,App\Models\Livreur'
        ]);
        
        // Create the communication
        $communication = Communication::create([
            'user_id' => auth()->id(),
            'sender_id' => Auth::id(),
            'sender_type' => 'App\Models\User', // Admin is a User
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
            'is_read' => false,
            'is_admin' => auth()->user()->is_admin ?? false

        ]);
        
        // Create a notification for the receiver
        Notification::create([
            'user_id' => $request->receiver_id,
            'type' => 'new_message',
            'message' => 'Nouveau message de l\'administrateur',
            'is_read' => false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès',
            'communication' => $communication
        ]);
    }
    
    // public function getNewMessages(Request $request)
    // {
    //     $request->validate([
    //         'receiver_id' => 'required|integer',
    //         'receiver_type' => 'required|string',
    //         'last_id' => 'required|integer'
    //     ]);
        
    //     $newCommunications = Communication::where(function($q) use ($request) {
    //             // Messages sent by admin to this receiver
    //             $q->where('sender_id', Auth::id())
    //               ->where('sender_type', 'App\Models\User')
    //               ->where('receiver_id', $request->receiver_id)
    //               ->where('receiver_type', $request->receiver_type);
    //         })
    //         ->orWhere(function($q) use ($request) {
    //             // Messages sent by this receiver to admin
    //             $q->where('receiver_id', Auth::id())
    //               ->where('receiver_type', 'App\Models\User')
    //               ->where('sender_id', $request->receiver_id)
    //               ->where('sender_type', $request->receiver_type);
    //         })
    //         ->where('id', '>', $request->last_id)
    //         ->latest()
    //         ->get();
            
    //     // Mark messages as read
    //     Communication::where('receiver_id', auth()->id())
    //                 ->where('receiver_type', 'App\Models\User')
    //                 ->where('sender_id', $request->receiver_id)
    //                 ->where('sender_type', $request->receiver_type)
    //                 ->where('is_read', false)
    //                 ->update(['is_read' => true]);
              
    //     return response()->json([
    //         'communications' => $newCommunications
    //     ]);
    // }

//     public function sendFromUser(Request $request)
// {
//     $request->validate([
//         'message' => 'required|string|max:500',
//         'receiver_id' => 'required|integer',
//         'receiver_type' => 'required|string|in:App\Models\User',
//     ]);

//     $user = auth()->user();

//     $communication = Communication::create([
//         'user_id' => $user->id,
//         'sender_id' => $user->id,
//         'sender_type' => 'App\Models\User',
//         'receiver_id' => $request->receiver_id,
//         'receiver_type' => $request->receiver_type,
//         'message' => $request->message,
//         'is_read' => false,
//         'is_admin' => false
//     ]);

//     // Créer une notification pour l’admin
//     Notification::create([
//         'user_id' => $request->receiver_id, // l'admin
//         'type' => 'new_message',
//         'message' => "Nouveau message de {$user->name}",
//         'is_read' => false
//     ]);

//     return redirect()->route('user.messages')->with('success', 'Message envoyé avec succès.');
// }

public function sendFromUser(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:App\Models\User', // L'admin est un utilisateur
        ]);

        // Enregistrer la communication
        $communication = Communication::create([
            'sender_id' => auth()->id(),
            'sender_type' => 'App\Models\User',
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
            'is_read' => false, // Message non lu initialement
        ]);

        // Créer une notification pour le destinataire (l'admin)
        Notification::create([
            'user_id' => $request->receiver_id, // L'admin
            'type' => 'new_message',
            'message' => "Nouveau message de l'utilisateur",
            'is_read' => false,
        ]);

        return redirect()->route('user.messages')->with('success', 'Message envoyé avec succès.');
    }

    // Cette méthode récupère les nouveaux messages pour l'utilisateur
    public function getNewMessages(Request $request)
    {
        $request->validate([
            'last_id' => 'required|integer'
        ]);

        // Récupérer les nouveaux messages depuis l'ID du dernier message
        $newCommunications = Communication::where('id', '>', $request->last_id)
                                          ->where(function($query) {
                                              $query->where('sender_id', Auth::id())
                                                    ->orWhere('receiver_id', Auth::id());
                                          })
                                          ->orderBy('created_at', 'asc')
                                          ->get();

        // Marquer les nouveaux messages comme lus
        Communication::whereIn('id', $newCommunications->pluck('id'))
                    ->update(['is_read' => true]);

        return response()->json(['communications' => $newCommunications]);
    }

    public function getUnreadCounts()
{
    // Pour l'admin - compter les messages non lus par client/livreur
    if (auth()->user()->is_admin) {
        $counts = Communication::where('receiver_id', auth()->id())
                             ->where('receiver_type', 'App\Models\User')
                             ->where('is_read', false)
                             ->selectRaw('sender_id, count(*) as count')
                             ->groupBy('sender_id')
                             ->pluck('count', 'sender_id');
        
        return response()->json(['counts' => $counts]);
    }
    
    // Pour les utilisateurs normaux (clients/livreurs)
    $unreadCount = Communication::where('receiver_id', auth()->id())
                              ->where('receiver_type', get_class(auth()->user()))
                              ->where('is_read', false)
                              ->count();
                              
    return response()->json(['count' => $unreadCount]);
}

}
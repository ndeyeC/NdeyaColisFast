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
        $users = User::where('role', 'client')->get();
        $livreurs = Livreur::all(); 
        
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
        
        $communication = Communication::create([
            'sender_id' => Auth::id(),
            'sender_type' => 'App\Models\User', 
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
            'is_read' => false,
        ]);
        
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
            'is_read' => false, 
        ]);

        Notification::create([
            'user_id' => $request->receiver_id, 
            'type' => 'new_message',
            'message' => "Nouveau message de l'utilisateur",
            'is_read' => false,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('user.messages')->with('success', 'Message envoyé avec succès.');
    }

    public function getNewMessages(Request $request)
    {
        $request->validate([
            'last_id' => 'required|integer'
        ]);

        $userId = Auth::id();
        $userType = get_class(Auth::user()); 

        $newCommunications = Communication::where('id', '>', $request->last_id)
            ->where(function($query) use ($userId, $userType) {
                $query->where(function($q) use ($userId, $userType) {
                    $q->where('sender_id', $userId)
                      ->where('sender_type', $userType);
                })
                ->orWhere(function($q) use ($userId, $userType) {
                    $q->where('receiver_id', $userId)
                      ->where('receiver_type', $userType);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        Communication::where('receiver_id', $userId)
            ->where('receiver_type', $userType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['communications' => $newCommunications]);
    }

    public function getUnreadCounts()
    {
        $userId = Auth::id();
        $userType = get_class(Auth::user());
        
        if (auth()->user()->is_admin) {
            $counts = Communication::where('receiver_id', $userId)
                                 ->where('receiver_type', $userType)
                                 ->where('is_read', false)
                                 ->selectRaw('sender_id, sender_type, count(*) as count')
                                 ->groupBy('sender_id', 'sender_type')
                                 ->get()
                                 ->mapWithKeys(function($item) {
                                     return ["{$item->sender_type}_{$item->sender_id}" => $item->count];
                                 });
            
            return response()->json(['counts' => $counts]);
        }
        
        $unreadCount = Communication::where('receiver_id', $userId)
                                  ->where('receiver_type', $userType)
                                  ->where('is_read', false)
                                  ->count();
                                  
        return response()->json(['count' => $unreadCount]);
    }

    public function getUserConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'user_type' => 'required|string|in:App\Models\User,App\Models\Livreur'
        ]);

        $adminId = Auth::id();
        $userId = $request->user_id;
        $userType = $request->user_type;

        $communications = Communication::where(function($query) use ($adminId, $userId, $userType) {
            $query->where('sender_id', $adminId)
                  ->where('sender_type', 'App\Models\User')
                  ->where('receiver_id', $userId)
                  ->where('receiver_type', $userType);
        })->orWhere(function($query) use ($adminId, $userId, $userType) {
            $query->where('sender_id', $userId)
                  ->where('sender_type', $userType)
                  ->where('receiver_id', $adminId)
                  ->where('receiver_type', 'App\Models\User');
        })->orderBy('created_at', 'asc')->get();

        // Marquer les messages reçus par l'admin comme lus
        Communication::where('receiver_id', $adminId)
                    ->where('receiver_type', 'App\Models\User')
                    ->where('sender_id', $userId)
                    ->where('sender_type', $userType)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

        return response()->json(['communications' => $communications]);
    }
}
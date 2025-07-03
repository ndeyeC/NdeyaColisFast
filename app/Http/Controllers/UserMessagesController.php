<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Communication;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class UserMessagesController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userType = get_class(Auth::user());
         $adminId = getAdminId();
        
        $communications = Communication::where(function($query) use ($userId, $userType, $adminId) {
            $query->where('sender_id', $userId)
                  ->where('sender_type', $userType)
                  ->where('receiver_id', $adminId)
                  ->where('receiver_type', 'App\Models\User');
        })->orWhere(function($query) use ($userId, $userType, $adminId) {
            $query->where('receiver_id', $userId)
                  ->where('receiver_type', $userType)
                  ->where('sender_id', $adminId)
                  ->where('sender_type', 'App\Models\User');
        })->orderBy('created_at', 'asc')->get();
        
        Communication::where('receiver_id', $userId)
                    ->where('receiver_type', $userType)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
        
        return view('user.messages', compact('communications'));
    }
    
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:App\Models\User',
        ]);

        $userId = Auth::id();
        $userType = get_class(Auth::user());

        // Enregistrer la communication
        $communication = Communication::create([
            'sender_id' => $userId,
            'sender_type' => $userType,
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Créer une notification pour l'admin
        Notification::create([
            'user_id' => $request->receiver_id,
            'type' => 'new_message',
            'message' => "Nouveau message de " . Auth::user()->name,
            'is_read' => false,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'communication' => $communication
            ]);
        }

        return redirect()->route('user.messages')->with('success', 'Message envoyé avec succès.');
    }
    
    public function checkNewMessages(Request $request)
    {
        $request->validate([
            'last_id' => 'required|integer'
        ]);

        $userId = Auth::id();
        $userType = get_class(Auth::user());
        $adminId = getAdminId();

        $newCommunications = Communication::where('id', '>', $request->last_id)
            ->where(function($query) use ($userId, $userType, $adminId) {
                $query->where('sender_id', $userId)
                      ->where('sender_type', $userType)
                      ->where('receiver_id', $adminId)
                      ->where('receiver_type', 'App\Models\User');
            })
            ->orWhere(function($query) use ($userId, $userType, $adminId) {
                $query->where('receiver_id', $userId)
                      ->where('receiver_type', $userType)
                      ->where('sender_id', $adminId)
                      ->where('sender_type', 'App\Models\User');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        Communication::where('receiver_id', $userId)
            ->where('receiver_type', $userType)
            ->where('sender_id', $adminId)
            ->where('sender_type', 'App\Models\User')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['communications' => $newCommunications]);
    }
}
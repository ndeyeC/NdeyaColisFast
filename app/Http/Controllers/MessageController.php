<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $communications = Communication::userConversations(Auth::id())->get();
        
        // Debug des messages (à supprimer en production)
        // dd($communications);
        
        return view('user.messages', [
            'communications' => $communications
        ]);
    }

    public function sendToAdmin(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500'
        ]);
        
        $adminId = 1; 
        
        $communication = Communication::create([
            'sender_id' => Auth::id(),
            'sender_type' => 'App\Models\User',
            'receiver_id' => $adminId,
            'receiver_type' => 'App\Models\User',
            'message' => $validated['message'],
            'is_read' => false
        ]);
        
        Notification::create([
            'user_id' => $adminId,
            'type' => 'new_message',
            'message' => 'Nouveau message de ' . Auth::user()->name,
            'is_read' => false
        ]);
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('user.messages')->with('success', 'Message envoyé!');
    }

    public function checkNewMessages(Request $request)
    {
        $validated = $request->validate([
            'last_id' => 'required|integer'
        ]);
        
        $communications = Communication::newMessagesForUser(
            Auth::id(),
            $validated['last_id']
        );
        
        foreach ($communications as $comm) {
            if ($comm->receiver_id === Auth::id() && !$comm->is_read) {
                $comm->is_read = true;
                $comm->save();
            }
        }
        
        return response()->json(['communications' => $communications]);
    }
}
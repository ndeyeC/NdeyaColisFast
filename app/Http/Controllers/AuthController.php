<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AuthController extends Controller
{
public function saveFcmToken(Request $request)
{
    $request->validate(['fcm_token' => 'required|string']);

    $user = Auth::user();
    $user->update(['fcm_token' => $request->fcm_token]);

    Log::info("FCM Token enregistré pour " . $user->name . " | Token : " . $request->fcm_token);
}


}

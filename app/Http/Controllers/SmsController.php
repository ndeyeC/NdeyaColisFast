<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwilioService;

class SmsController extends Controller
{
    public function envoyerSms(TwilioService $twilio)
    {
        $twilio->sendSms('+221783862193', 'Votre commande a été livrée avec succès.');
        return response()->json(['message' => 'SMS envoyé']);
    }
}

<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Commnande;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'commande_id' => 'required|exists:commnandes,id',
            'rating' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $commande = Commnande::findOrFail($request->commande_id);

        if ($commande->user_id !== auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas noter une livraison qui ne vous appartient pas.');
        }

        $existing = Evaluation::where('commande_id', $commande->id)
            ->where('type_evaluation', 'client')
            ->first();

        if ($existing) {
            return back()->with('error', 'Vous avez déjà noté cette livraison.');
        }

        Evaluation::create([
            'commande_id' => $commande->id,
            'user_id' => auth()->id(),
            'driver_id' => $commande->driver_id,
            'note' => $request->rating,
            'commentaire' => $request->commentaire,
            'type_evaluation' => 'client',
        ]);

        return back()->with('success', 'Merci pour votre avis !');
    }
}

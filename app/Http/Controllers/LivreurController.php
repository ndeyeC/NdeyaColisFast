<?php

namespace App\Http\Controllers;

use App\Models\Livreur;
use App\Models\User; 
use App\Notifications\AdminLivreurCreatedNotification;
use Illuminate\Http\Request;

class LivreurController extends Controller
{
    public function index()
{
     $livreurs = User::where('role', 'livreur')->paginate(10); // Récupère tous les livreurs
    return view('admin.livreurs.index', compact('livreurs'));

    // Dans LivreurController.php
// $livreurs = User::where('role', 'livreur')
// ->join('livreurs', 'users.id', '=', 'livreurs.user_id')
// ->select('users.*', 'livreurs.vehicule')
// ->paginate(10);
}
public function show($id)
{
    $livreur = User::where('role', 'livreur')->where('user_id', $id)->firstOrFail();
    return view('admin.livreurs.show', compact('livreur'));
}

// Exemple dans LivreurController.php
public function showJson($id)
{
    // Récupère le livreur sans la comptabilisation des livraisons
    $livreur = Livreur::findOrFail($id);

    return response()->json([
        'livreur' => $livreur
    ]);
}





public function destroy(User $livreur)
{
    $livreur->delete(); // Supprime le livreur
    return redirect()->route('admin.livreurs.index')->with('success', 'Livreur supprimé avec succès.');
}



}

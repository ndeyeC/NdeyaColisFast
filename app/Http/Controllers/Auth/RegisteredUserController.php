<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
{
    
    
    // Validation commune
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'numero_telephone' => ['required', 'string', 'max:255'],
        'role' => ['required', 'in:client,livreur,admin'],
    ];

    // Validation conditionnelle selon rôle
    if ($request->role === 'client') {
        $rules['adress'] = ['required', 'string', 'max:255'];
    } elseif ($request->role === 'livreur') {
        $rules['vehicule'] = ['required', 'string', 'max:255'];
        $rules['id_card'] = ['required', 'string', 'max:255'];
        $rules['type_livreur'] = ['required', 'in:urbain,classique']; // ✅ Nouveau champ
    }

    $validated = $request->validate($rules);

    // Préparer les données de création
    $userData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'numero_telephone' => $validated['numero_telephone'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
    ];

    // Ajouter les champs spécifiques selon rôle
    if ($validated['role'] === 'client') {
        $userData['adress'] = $validated['adress'];
    } elseif ($validated['role'] === 'livreur') {
        $userData['vehicule'] = $validated['vehicule'];
        $userData['id_card'] = $validated['id_card'];
          $userData['type_livreur'] = $validated['type_livreur']; // ✅ On stocke le type
        
    }

    // Création utilisateur
    $user = User::create($userData);
   

    event(new Registered($user));

    Auth::login($user);

    // Redirection selon rôle
    switch ($user->role) {
        case 'client':
            return redirect()->route('dashboard');
        case 'livreur':
            return redirect()->route('livreur.dashboarde');
        case 'admin':
            return redirect()->route('admin.dashboard');
        default:
            Auth::logout();
            return redirect()->route('login')->withErrors(['role' => 'Rôle non autorisé.']);
    }
}
}
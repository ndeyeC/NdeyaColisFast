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
    

    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display the registration view.
     */
   public function store(Request $request): RedirectResponse
{
    // Validation commune
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'numero_telephone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
        'role' => ['required', 'in:client,livreur,admin'],
    ];

    if ($request->role === 'client') {
        $rules['adress'] = ['required', 'string', 'max:255'];
    } elseif ($request->role === 'livreur') {
        $rules['vehicule'] = ['required', 'string', 'max:255'];
        $rules['id_card'] = ['required', 'regex:/^[0-9]{8,15}$/']; 
        $rules['type_livreur'] = ['required', 'in:urbain,classique'];
    }

    $validated = $request->validate($rules);

    // ✅ Vérification si un livreur supprimé existe déjà
    if (User::withTrashed()->where('email', $validated['email'])->whereNotNull('deleted_at')->exists()) {
        return back()->withErrors(['email' => 'Ce compte a été supprimé et ne peut pas être recréé.']);
    }

    $userData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'numero_telephone' => $validated['numero_telephone'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
    ];

    if ($validated['role'] === 'client') {
        $userData['adress'] = $validated['adress'];
    } elseif ($validated['role'] === 'livreur') {
        $userData['vehicule'] = $validated['vehicule'];
        $userData['id_card'] = $validated['id_card'];
        $userData['type_livreur'] = $validated['type_livreur']; 
    }

    $user = User::create($userData);

    event(new Registered($user));
    Auth::login($user);

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
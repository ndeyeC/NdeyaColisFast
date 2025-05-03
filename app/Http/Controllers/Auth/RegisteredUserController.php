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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'numero_telephone' =>['required','string','max:255'],
            'adress' =>['nullable','string','max:255'],
            'role' => ['required', 'in:admin,client,livreur'],


        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
             'numero_telephone' => $request->numero_telephone,
             'adress' => $request->adress,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Inscription réussie! Veuillez vous connecter.');

        // Auth::login($user);permet a l utilisateur de se connecter automatiquemnt pas besoin de renseigner son email et password

        
        

        
    

    }
}

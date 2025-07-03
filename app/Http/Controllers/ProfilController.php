<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function edit()
    {
        return view('profil.edit', ['user' => Auth::user()]);
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->user_id.',user_id',
            'numero_telephone' => 'nullable|string|max:20',
        ]);
    
        $user->update($request->only(['name', 'email', 'numero_telephone']));
    
        return redirect()->route('client.dashboard')->with('success', 'Profil mis à jour avec succès');
    }
}
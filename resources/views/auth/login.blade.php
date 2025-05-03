<div class="flex flex-col md:flex-row min-h-screen">
    <!-- Section de gauche avec texte et image d'arrière-plan (50%) -->
    <div class="w-full md:w-1/2 bg-indigo-600 flex items-center justify-center p-8 text-white relative mt-8">
        <!-- Image d'arrière-plan -->
        <div class="absolute inset-0 z-0 opacity-20" style="background-image: url('{{ asset('image/logo1.png') }}'); background-size: cover; background-position: center; width: 110%;"></div>
        
        <!-- Contenu centré -->
        <div class="max-w-md z-10 relative flex flex-col items-center justify-center text-center min-h-[80vh] mx-auto">
            <p class="mb-6 text-lg">Livraison rapide et sécurisée partout au Sénégal. Suivez vos colis en temps réel et profitez d'un service client de qualité.</p>
            <ul class="space-y-4">
                <li class="flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Livraison express en 24h
                </li>
                <li class="flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Suivi en temps réel
                </li>
                <li class="flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Paiement sécurisé
                </li>
            </ul>
        </div>
    </div>

    <!-- Section de droite avec le composant x-guest-layout contenant uniquement le formulaire (50%) -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-12 lg:p-16 bg-gray-50">
        <x-guest-layout class="w-full max-w-md">     
            <!-- Session Status -->     
            <x-auth-session-status class="mb-4" :status="session('status')" />
                 
            <div class="text-center mb-6">         
                <h2 class="text-2xl font-bold text-gray-800">Connexion</h2>         
                <p class="text-gray-600">Accédez à votre compte</p>     
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="w-full">         
                @csrf
                
                <!-- Email Address -->         
                <div>             
                    <x-input-label for="email" :value="__('Email')" />             
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />             
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />         
                </div>
                
                <!-- Password -->         
                <div class="mt-4">             
                    <x-input-label for="password" :value="__('Mot de passe')" />                          
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />                          
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />         
                </div>
                
                <!-- Role Selection -->         
                <div class="mt-4">             
                    <x-input-label for="role" :value="__('Se connecter en tant que')" />             
                    <select id="role" name="role" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">                 
                        <option value="client">Client</option>                 
                        <option value="livreur">Livreur</option>                 
                        <option value="admin">Administrateur</option>             
                    </select>         
                </div>
                
                <!-- Remember Me -->         
                <div class="block mt-4">             
                    <label for="remember_me" class="inline-flex items-center">                 
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">                 
                        <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>             
                    </label>         
                </div>
                
                <div class="flex items-center justify-between mt-4">             
                    @if (Route::has('password.request'))                 
                        <a class="text-sm text-gray-600 hover:text-gray-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">                     
                            {{ __('Mot de passe oublié?') }}                 
                        </a>             
                    @endif
                    
                    <x-primary-button class="ms-3">                 
                        {{ __('Connexion') }}             
                    </x-primary-button>         
                </div>
                
                <div class="text-center mt-6 pt-4 border-t border-gray-200">             
                    <p class="text-sm text-gray-600">Vous n'avez pas de compte?</p>             
                    <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Créer un compte</a>         
                </div>     
            </form> 
        </x-guest-layout>
    </div>
</div>
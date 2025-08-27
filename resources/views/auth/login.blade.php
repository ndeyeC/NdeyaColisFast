<div class="flex flex-col md:flex-row min-h-screen">
    <!-- Section de gauche avec image et infos -->
    <div class="w-full md:w-1/2 bg-red-600 flex items-center justify-center p-8 text-white relative mt-8">
        <!-- <div class="absolute inset-0 z-0 opacity-20" style="background-image: url('{{ asset('image/colis.jpg') }}'); background-size: cover; background-position: center; width: 110%;"></div> -->
     <img src="{{ asset('image/colis.jpg') }}" class="w-full h-auto rounded-lg mb-4" alt="Colis">

        <div class="max-w-md z-10 relative flex flex-col items-center justify-center text-center min-h-[80vh] mx-auto">
            <p class="mb-6 text-lg"></p>
            <ul class="space-y-4">
                @for ($i = 0; $i < 3; $i++)
                    <li class="flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                @endfor
            </ul>
        </div>
    </div>

    <!-- Section de droite avec formulaire -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-12 lg:p-16 bg-gray-50">
        <x-guest-layout class="w-full max-w-md">

            <!-- Lien de retour -->
            <div class="text-left mb-4">
                <a href="{{ route('welcome') }}" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2L2 10h3v6h4v-4h2v4h4v-6h3L10 2z" />
                    </svg>
                    Retour à l’accueil
                </a>
            </div>

            <!-- Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-red-600">Connexion</h2>
                <p class="text-gray-600">Accédez à votre compte</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="w-full">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Mot de passe -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>


                <!-- Souvenir -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
                    </label>
                </div>

                <!-- Bouton et lien mot de passe oublié -->
                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" href="{{ route('password.request') }}">
                            {{ __('Mot de passe oublié?') }}
                        </a>
                    @endif

                    <x-primary-button class="ms-3 bg-red-600 hover:bg-red-700 text-white font-bold">
                        {{ __('Connexion') }}
                    </x-primary-button>
                </div>

                <!-- Lien inscription -->
                <div class="text-center mt-6 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">Vous n'avez pas de compte ?</p>
                    <a href="{{ route('register') }}" class="text-red-600 hover:text-red-800 font-bold">Créer un compte</a>
                </div>
            </form>
        </x-guest-layout>
    </div>
</div>

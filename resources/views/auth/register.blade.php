<div class="flex flex-col md:flex-row min-h-screen">
    <!-- Section de gauche avec texte et image d'arrière-plan (50%) -->
    <div class="w-full md:w-1/2 bg-indigo-600 flex items-center justify-center p-8 text-white relative mt-8">
        <!-- Image d'arrière-plan -->
        <div class="absolute inset-0 z-0 opacity-20" style="background-image: url('{{ asset('image/logo1.png') }}'); background-size: cover; background-position: center; width: 110%;height:85%;"></div>
        
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
    <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-12 lg:p-16 bg-gray-50 w-50 h-100">
        <x-guest-layout class="w-full max-w-sm">     
            <!-- Session Status -->     
            <x-auth-session-status class="mb-4" :status="session('status')" />
                 
            <div class="text-center mb-6">         
                <h2 class="text-2xl font-bold text-gray-800">Créer un compte colisFast</h2>         
                <p class="text-gray-600">Rejoignez la plateforme de livraison la plus rapide du Sénégal</p>     
            </div>
            
            <form method="POST" action="{{ route('register') }}" class="w-full w-50">         
                @csrf
                
                <!-- Role Selection -->         
                <!-- <div>             
                    <x-input-label for="user_id" :value="__('Je suis un')" />             
                    <select id="user_id" name="user_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="client">Client</option>
                        <option value="livreur">Livreur</option>
                    </select>         
                </div>
                 -->

                 <div>
                     <x-input-label for="role" :value="__('Je suis un')" />
                     <select id="role" name="role" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                     <option value="client">Client</option>
                     <option value="livreur">Livreur</option>
                     </select>
                  </div>

                <!-- Name -->         
                <div class="mt-4">             
                    <x-input-label for="name" :value="__('Nom complet')" />             
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />             
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />         
                </div>
                
                <!-- Email Address -->         
                <div class="mt-4">             
                    <x-input-label for="email" :value="__('Email')" />             
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />             
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />         
                </div>

                <!-- Phone Number -->         
                <div class="mt-4">             
                    <x-input-label for="numero_telephone" :value="__('Numéro de téléphone')" />             
                    <x-text-input id="numero_telephone" class="block mt-1 w-full" type="numero_telephone" name="numero_telephone" :value="old('numero_telephone')" required />             
                    <x-input-error :messages="$errors->get('numero_telephone')" class="mt-2" />         
                </div>

                <!-- Address - Only for clients -->
                <div class="mt-4 client-field">
                    <x-input-label for="adress" :value="__('Adresse')" />
                    <x-text-input id="adress" class="block mt-1 w-full" type="text" name="adress" :value="old('adress')" />
                    <x-input-error :messages="$errors->get('adress')" class="mt-2" />
                </div>

                <!-- Vehicle Type - Only for delivery people -->
         <div class="mt-4 livreur-field hidden">
                <x-input-label for="vehicule" :value="__('Type de véhicule')" />
              <select id="vehicule" name="vehicule" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                 <option value="" disabled selected>-- Choisissez --</option>
                <option value="moto">Moto</option>
                <option value="voiture">Voiture</option>
                 <option value="velo">Vélo</option>
                <option value="autre">Autre</option>
              </select>
           <x-input-error :messages="$errors->get('vehicule')" class="mt-2" />
         </div>


                <!-- ID Card - Only for delivery people -->
                <div class="mt-4 livreur-field hidden">
                    <x-input-label for="id_card" :value="__('Numéro de carte d\'identité')" />
                    <x-text-input id="id_card" class="block mt-1 w-full" type="text" name="id_card" :value="old('id_card')" />
                    <x-input-error :messages="$errors->get('id_card')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Mot de passe')" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                  type="password"
                                  name="password"
                                  required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmer mot de passe')" />

                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                  type="password"
                                  name="password_confirmation" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Terms and Conditions -->
                <div class="mt-4">
                    <label for="terms" class="inline-flex items-center">
                        <input id="terms" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="terms" required>
                        <span class="ml-2 text-sm text-gray-600">{{ __('J\'accepte les') }} <a href="#" class="text-blue-500 hover:text-blue-700">termes et conditions</a></span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        {{ __('Déjà inscrit?') }}
                    </a>

                    <x-primary-button class="ms-4">
                        {{ __('S\'inscrire') }}
                    </x-primary-button>
                </div>
            </form>
        </x-guest-layout>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role'); 
    const clientFields = document.querySelectorAll('.client-field');
    const livreurFields = document.querySelectorAll('.livreur-field');

    function toggleFields() {
        const isLivreur = roleSelect.value === 'livreur';
        
        clientFields.forEach(field => field.classList.toggle('hidden', isLivreur));
        livreurFields.forEach(field => field.classList.toggle('hidden', !isLivreur));
    }

    roleSelect.addEventListener('change', toggleFields);
    toggleFields(); // Initialisation
});

</script>
@endpush

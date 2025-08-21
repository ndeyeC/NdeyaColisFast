<div class="flex flex-col md:flex-row min-h-screen">
    <!-- Section gauche -->
    <div class="w-full md:w-1/2 bg-indigo-600 flex items-center justify-center p-8 text-white relative mt-8">
        <div class="absolute inset-0 z-0 opacity-20" style="background-image: url('{{ asset('image/colis.jpg') }}'); background-size: cover; background-position: center; width: 110%; height: 85%;"></div>
        <div class="max-w-md z-10 relative flex flex-col items-center justify-center text-center min-h-[80vh] mx-auto">
            <p class="mb-6 text-lg"></p>
        </div>
    </div>

    <!-- Section droite avec formulaire -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-12 lg:p-16 bg-gray-50">
        <x-guest-layout class="w-full max-w-sm">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Créer un compte colisFast</h2>
                <p class="text-gray-600">Rejoignez la plateforme de livraison la plus rapide du Sénégal</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="w-full">
                @csrf

                <!-- Rôle -->
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Je suis un')" />
                    <select id="role" name="role" class="block mt-1 w-full" required>
                        <option value="">-- Choisissez --</option>
                        <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>Client</option>
                        <option value="livreur" {{ old('role') === 'livreur' ? 'selected' : '' }}>Livreur</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Nom -->
                <div class="mt-4">
                    <x-input-label for="name" :value="__('Nom complet')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                        :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                        :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Téléphone -->
                <div class="mt-4">
                    <x-input-label for="numero_telephone" :value="__('Numéro de téléphone')" />
                    <x-text-input id="numero_telephone" class="block mt-1 w-full" type="text"
                        name="numero_telephone" :value="old('numero_telephone')" required />
                    <x-input-error :messages="$errors->get('numero_telephone')" class="mt-2" />
                </div>

                <!-- Adresse (client uniquement) -->
                <div class="mt-4 client-field">
                    <x-input-label for="adress" :value="__('Adresse')" />
                    <x-text-input id="adress" class="block mt-1 w-full" type="text" name="adress"
                        :value="old('adress')" />
                    <x-input-error :messages="$errors->get('adress')" class="mt-2" />
                </div>

                <!-- Véhicule (livreur uniquement) -->
                <div class="mt-4 livreur-field hidden">
                    <x-input-label for="vehicule" :value="__('Type de véhicule')" />
                    <select id="vehicule" name="vehicule" class="block mt-1 w-full">
                        <option value="" disabled {{ old('vehicule') ? '' : 'selected' }}>-- Choisissez --</option>
                        <option value="moto" {{ old('vehicule') === 'moto' ? 'selected' : '' }}>Moto</option>
                        <option value="voiture" {{ old('vehicule') === 'voiture' ? 'selected' : '' }}>Voiture</option>
                        <option value="velo" {{ old('vehicule') === 'velo' ? 'selected' : '' }}>Vélo</option>
                        <option value="autre" {{ old('vehicule') === 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    <x-input-error :messages="$errors->get('vehicule')" class="mt-2" />
                </div>

                <!-- Carte d'identité (livreur uniquement) -->
                <div class="mt-4 livreur-field hidden">
                    <x-input-label for="id_card" :value="__('Numéro de carte d\'identité')" />
                    <x-text-input id="id_card" type="text" name="id_card" class="block mt-1 w-full"
                        :value="old('id_card')" />
                    <x-input-error :messages="$errors->get('id_card')" class="mt-2" />
                </div>
                <!-- ✅ Type de livreur (urbain ou classique) -->
<div class="mt-4 livreur-field hidden">
    <x-input-label for="type_livreur" :value="__('Type de livreur')" />
    <select id="type_livreur" name="type_livreur" class="block mt-1 w-full">
        <option value="" disabled selected>-- Choisissez --</option>
        <option value="urbain" {{ old('type_livreur') === 'urbain' ? 'selected' : '' }}>Livreur Urbain</option>
        <option value="classique" {{ old('type_livreur') === 'classique' ? 'selected' : '' }}>Livreur Classique</option>
    </select>
    <x-input-error :messages="$errors->get('type_livreur')" class="mt-2" />
</div>

                <!-- Mot de passe -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirmation mot de passe -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmer mot de passe')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                        name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Conditions -->
                <div class="mt-4">
                    <label for="terms" class="inline-flex items-center">
                        <input id="terms" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            name="terms" required>
                        <span class="ml-2 text-sm text-gray-600">
                            J'accepte les <a href="#" class="text-blue-500 hover:text-blue-700">termes et conditions</a>
                        </span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('login') }}">
                        Déjà inscrit ?
                    </a>

                    <x-primary-button class="ms-4">
                        S'inscrire
                    </x-primary-button>
                </div>
            </form>
        </x-guest-layout>
    </div>
</div>

<!-- Script inclus directement pour affichage dynamique -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const clientFields = document.querySelectorAll('.client-field');
        const livreurFields = document.querySelectorAll('.livreur-field');

        function toggleFields() {
            const selectedRole = roleSelect.value;
            const isClient = selectedRole === 'client';
            const isLivreur = selectedRole === 'livreur';

            clientFields.forEach(el => el.classList.toggle('hidden', !isClient));
            livreurFields.forEach(el => el.classList.toggle('hidden', !isLivreur));
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', toggleFields);
            toggleFields(); 
        }
    });
</script>

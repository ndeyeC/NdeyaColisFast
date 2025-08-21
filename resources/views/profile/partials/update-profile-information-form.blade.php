<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informations du profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Mettez à jour vos informations personnelles et votre adresse email.") }}
        </p>
    </header>

    <!-- Formulaire pour renvoyer le mail de vérification -->
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Formulaire principal de mise à jour du profil -->
    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT') <!-- méthode correcte -->

        <!-- Nom -->
        <div>
            <x-input-label for="name" :value="__('Nom')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Votre adresse email n’est pas vérifiée.') }}
                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        {{ __('Cliquez ici pour renvoyer le mail de vérification') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('Un nouveau lien de vérification a été envoyé.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- Numéro de téléphone -->
        <div>
            <x-input-label for="numero_telephone" :value="__('Téléphone')" />
            <x-text-input id="numero_telephone" name="numero_telephone" type="text" class="mt-1 block w-full"
                :value="old('numero_telephone', $user->numero_telephone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('numero_telephone')" />
        </div>

        <!-- Bouton Enregistrer -->
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-red-600 hover:bg-red-700">{{ __('Enregistrer') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">{{ __('Enregistré.') }}</p>
            @endif
        </div>
    </form>
</section>

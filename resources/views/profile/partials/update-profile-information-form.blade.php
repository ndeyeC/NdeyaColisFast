<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informations du profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Consultez vos informations personnelles et votre adresse email.") }}
        </p>
    </header>

    <!-- Nom -->
    <div class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Nom')" />
            <x-text-input id="name" type="text" class="mt-1 block w-full" :value="$user->name" readonly />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" class="mt-1 block w-full" :value="$user->email" readonly />
        </div>

        <!-- Email non vérifiée -->
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="text-sm mt-2 text-gray-800">
                {{ __('Votre adresse email n’est pas vérifiée.') }}
            </p>
        @endif

        <!-- Numéro de téléphone -->
        <div>
            <x-input-label for="numero_telephone" :value="__('Téléphone')" />
            <x-text-input id="numero_telephone" type="text" class="mt-1 block w-full" :value="$user->numero_telephone" readonly />
        </div>
    </div>
</section>

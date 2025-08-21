@extends('layouts.template')

@section('title', 'Nouvelle Commande')

@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Retour
        </a>
    </div>

    <h2 class="text-2xl font-bold mb-4">Créer une nouvelle commande</h2>

    {{-- Messages de session --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Erreurs de validation --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Informations sur les jetons --}}
    @if(Auth::user()->token_balance > 0)
        <div class="mb-6 p-4 border-2 border-red-200 rounded-lg bg-red-50">
            <div class="flex items-center mb-2">
                <i class="fas fa-coins text-yellow-500 mr-2"></i>
                <span class="font-bold text-red-600">Vous avez {{ Auth::user()->token_balance }} jetons disponibles</span>
            </div>
        </div>
    @endif

    <form action="{{ route('commnandes.store') }}" method="POST" id="orderForm">
        @csrf

        {{-- Mode de paiement --}}
        @if(Auth::user()->token_balance > 0)
            <div class="mb-6 p-4 border rounded-lg bg-red-50">
                <label class="block font-medium mb-3">Mode de paiement</label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition" id="tokenOption">
                        <input type="radio" name="mode_paiement" value="tokens" class="mr-3" id="tokenRadio">
                        <div>
                            <span class="font-medium text-red-600">
                                <i class="fas fa-coins text-yellow-500 mr-1"></i>
                                Utiliser mes jetons ({{ Auth::user()->token_balance }} disponibles)
                            </span>
                            <p class="text-xs text-red-600 mt-1">Valide uniquement pour Dakar</p>
                        </div>
                    </label>
                   
                </div>
            </div>
        @else
            <input type="hidden" name="mode_paiement" value="payment">
        @endif

        {{-- Champ départ avec suggestions --}}
        <div class="mb-4 relative">
            <label class="block font-medium">Adresse de départ</label>
            <input type="text" name="adresse_depart" id="adresse_depart" class="w-full p-3 border rounded" value="{{ old('adresse_depart') }}" required>
            <ul id="suggestions_depart" class="absolute z-10 bg-white border rounded w-full mt-1"></ul>
        </div>

        <div id="precisions_depart_box" class="mb-4" style="display: none;">
            <label class="block font-medium">Précisez l'adresse exacte à <span id="ville_depart_label"></span></label>
            <input type="text" name="details_adresse_depart" class="w-full p-3 border rounded" value="{{ old('details_adresse_depart') }}" placeholder="Saisissez une adresse, ex. Mosquée Massalikul Jinaan, HLM, ou utilisez un quartier connu">
        </div>

        {{-- Champ arrivée avec suggestions --}}
        <div class="mb-4 relative">
            <label class="block font-medium">Adresse de destination</label>
            <input type="text" name="adresse_arrivee" id="adresse_arrivee" class="w-full p-3 border rounded" value="{{ old('adresse_arrivee') }}" required>
            <ul id="suggestions_arrivee" class="absolute z-10 bg-white border rounded w-full mt-1"></ul>
        </div>

        <div id="precisions_arrivee_box" class="mb-4" style="display: none;">
            <label class="block font-medium">Précisez l'adresse exacte à <span id="ville_arrivee_label"></span></label>
            <input type="text" name="details_adresse_arrivee" class="w-full p-3 border rounded" value="{{ old('details_adresse_arrivee') }}" placeholder="Saisissez une adresse, ex. Mosquée Massalikul Jinaan, HLM, ou utilisez un quartier connu">
        </div>

        {{-- Messages d'alerte pour les jetons --}}
        <div id="token_alert" class="hidden mb-4 p-3 rounded-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span id="token_alert_message"></span>
        </div>

        @if(session('adresse_error'))
            <div class="bg-yellow-100 text-yellow-800 p-3 rounded mb-4">
                {{ session('adresse_error') }}
            </div>
        @endif

        <div class="mb-4">
            <label class="block font-medium">Type de colis</label>
            <select name="type_colis" class="w-full p-3 border rounded" required>
                <option value="">-- Sélectionner --</option>
                <option value="0-5 kg">0-5 kg</option>
                <option value="5-20 kg">5-20 kg</option>
                <option value="20-50 kg">20-50 kg</option>
                <option value="50+ kg">50+ kg</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Numéro de téléphone</label>
            <input type="tel" name="numero_telephone" class="w-full p-3 border rounded" value="{{ old('numero_telephone') }}" placeholder="Ex: 77 123 45 67" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Type de livraison</label>
            <select name="type_livraison" class="w-full p-3 border rounded" required>
                <option value="standard">Standard</option>
                <option value="express">Express</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Prix estimé</label>
            <input type="text" id="prix_affichage" class="w-full p-3 border rounded bg-gray-100" readonly>
            <input type="hidden" name="prix" id="prix_hidden">
            <input type="hidden" name="region_depart" id="region_depart">
            <input type="hidden" name="region_arrivee" id="region_arrivee">
            <input type="hidden" name="type_zone" id="type_zone">
            <input type="hidden" name="delivery_zone_id" id="delivery_zone_id">
        </div>

        <button type="submit" id="submitBtn" class="w-full bg-red-600 hover:bg-red-700 text-white py-4 rounded-xl text-xl font-bold shadow-lg transition transform hover:scale-[1.02]">
            Confirmer la commande
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tarifs = @json($tarifs ?? []);
    const zones = @json($zones ?? []);
    const deliveryZones = @json($deliveryZones ?? []);
    const validTokens = @json($validTokens ?? []);
    const userTokenBalance = {{ Auth::user()->token_balance ?? 0 }};
    const jetonPrice = @json($jetonPrice ?? 0);
    const jetonZoneName = @json($jetonZoneName ?? 'Dakar');

    // Région pour les jetons
    const dakarRegion = 'Dakar';

    // Liste des régions pour suggestions
    const allRegions = new Set();
    zones.forEach(zone => {
        allRegions.add(zone.region_depart);
        allRegions.add(zone.region_arrivee);
    });
    const regionsConnues = Array.from(allRegions);
    console.log('Regions disponibles:', regionsConnues); // Debug: vérifier les régions

    if (regionsConnues.length === 0) {
        console.warn('Aucune région disponible pour les suggestions');
        document.getElementById('token_alert').classList.remove('hidden');
        document.getElementById('token_alert').classList.add('bg-yellow-100', 'text-yellow-800');
        document.getElementById('token_alert_message').textContent = 'Aucune région disponible. Veuillez contacter l\'administrateur.';
    }

    // Champs
    const tokenRadio = document.getElementById('tokenRadio');
    const paymentRadios = document.querySelectorAll('input[name="mode_paiement"]');
    const tokenAlert = document.getElementById('token_alert');
    const tokenAlertMessage = document.getElementById('token_alert_message');
    const submitBtn = document.getElementById('submitBtn');
    const departInput = document.getElementById('adresse_depart');
    const arriveeInput = document.getElementById('adresse_arrivee');
    const prixAffichage = document.getElementById('prix_affichage');
    const prixHidden = document.getElementById('prix_hidden');
    const regionDepartInput = document.getElementById('region_depart');
    const regionArriveeInput = document.getElementById('region_arrivee');
    const typeZoneInput = document.getElementById('type_zone');
    const deliveryZoneIdInput = document.getElementById('delivery_zone_id');

    const normaliserTexte = t => {
        if (!t) return '';
        const texte = t.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        console.log('Texte normalisé:', texte); // Debug: vérifier la normalisation
        return texte;
    };

    // Extraire région depuis adresse
    function extraireRegionDepuisAdresse(adresse) {
        if (!adresse) return null;
        const adresseNorm = normaliserTexte(adresse);
        console.log('Adresse normalisée:', adresseNorm); // Debug

        // Si mode jetons est sélectionné et utilisateur a des jetons, restreindre à Dakar
        if (userTokenBalance > 0 && tokenRadio?.checked) {
            if (adresseNorm.includes(normaliserTexte(dakarRegion))) {
                console.log('Région détectée pour jetons:', dakarRegion); // Debug
                return dakarRegion;
            }
            console.log('Aucune région Dakar détectée pour jetons'); // Debug
            return null;
        }

        // Sinon, suggérer toutes les régions disponibles
        for (const region of regionsConnues) {
            if (adresseNorm.includes(normaliserTexte(region))) {
                console.log('Région détectée:', region); // Debug
                return region;
            }
        }
        console.log('Aucune région détectée'); // Debug
        return null;
    }

    // Vérifier compatibilité jetons
   function verifierCompatibiliteJetons() {
    if (!tokenRadio) return true; // Pas de jetons, donc pas de restriction
    const regionDepart = extraireRegionDepuisAdresse(departInput.value);
    const regionArrivee = extraireRegionDepuisAdresse(arriveeInput.value);

    console.log('Vérification jetons:', { regionDepart, regionArrivee, tokenRadioChecked: tokenRadio?.checked }); // Debug

    if (tokenRadio.checked) {
        if (userTokenBalance < 1) {
            tokenAlert.classList.remove('hidden');
            tokenAlert.classList.add('bg-red-100', 'text-red-800');
            tokenAlertMessage.textContent = 'Vous n’avez pas assez de jetons pour cette livraison.';
            submitBtn.disabled = true;
            return false;
        } else {
            // ✅ Plus de restriction sur Dakar
            tokenAlert.classList.add('hidden');
            submitBtn.disabled = false;
            return true;
        }
    } else {
        tokenAlert.classList.add('hidden');
        submitBtn.disabled = false;
        return true;
    }
}


    // Calculer le prix
    function calculerPrix() {
        const regionDepart = extraireRegionDepuisAdresse(departInput.value);
        const regionArrivee = extraireRegionDepuisAdresse(arriveeInput.value);
        const typeColis = document.querySelector('[name="type_colis"]')?.value;
        const typeLivraison = document.querySelector('[name="type_livraison"]')?.value;

        console.log('Calcul prix:', { regionDepart, regionArrivee, typeColis, typeLivraison }); // Debug

        if (!regionDepart || !regionArrivee || !typeColis || !typeLivraison) {
            prixAffichage.value = 'Veuillez remplir tous les champs';
            prixHidden.value = '';
            deliveryZoneIdInput.value = ''; // Ensure empty for incomplete inputs
            return;
        }

        // Mettre à jour les champs cachés
        regionDepartInput.value = regionDepart;
        regionArriveeInput.value = regionArrivee;

        // Récupérer l'ID de la zone Dakar pour les jetons
        const dakarZone = deliveryZones.find(z => z.name === dakarRegion);
        console.log('Zone Dakar:', dakarZone); // Debug

        if (tokenRadio?.checked && dakarZone && verifierCompatibiliteJetons()) {
            deliveryZoneIdInput.value = dakarZone.id || ''; // Set numeric ID for tokens
            typeZoneInput.value = 'Dakar'; // Par défaut pour jetons
            let prixJeton = jetonPrice;
            if (dakarZone && dakarZone.base_token_price) {
                prixJeton = dakarZone.base_token_price;
            }

            if (!prixJeton || prixJeton === 0) {
                console.warn('Prix du jeton non défini');
                prixAffichage.value = 'Erreur : Prix du jeton non disponible';
                prixHidden.value = '';
                deliveryZoneIdInput.value = ''; // Reset if no price
                return;
            }

            prixAffichage.value = `${prixJeton} FCFA (payé avec 1 jeton)`;
            prixHidden.value = prixJeton;
            console.log('Prix jeton calculé:', prixJeton, 'delivery_zone_id:', dakarZone.id); // Debug
            return;
        }

        // Tarif normal (paiement en ligne)
        deliveryZoneIdInput.value = ''; // Explicitly set to empty for online payment
        typeZoneInput.value = ''; // Sera défini par la zone correspondante
        const zone = zones.find(z => z.region_depart === regionDepart && z.region_arrivee === regionArrivee);
        console.log('Zone trouvée:', zone); // Debug

        if (!zone) {
            prixAffichage.value = 'Nous ne livrons pas actuellement dans ces zones.';
            prixHidden.value = '';
            typeZoneInput.value = '';
            return;
        }

        const tarif = tarifs.find(t => t.type_zone === zone.type_zone && t.tranche_poids === typeColis);
        console.log('Tarif trouvé:', tarif); // Debug

        if (!tarif) {
            prixAffichage.value = 'Tarif non disponible';
            prixHidden.value = '';
            typeZoneInput.value = '';
            return;
        }

        prixAffichage.value = `${tarif.prix} FCFA`;
        prixHidden.value = tarif.prix;
        typeZoneInput.value = zone.type_zone;
    }

    // Suggestions
    function setupSuggestion(inputId, suggestionsBoxId) {
        const input = document.getElementById(inputId);
        const suggestionsBox = document.getElementById(suggestionsBoxId);

        input.addEventListener('input', function() {
            const valeur = normaliserTexte(input.value);
            suggestionsBox.innerHTML = '';
            if (!valeur) {
                console.log('Valeur vide, pas de suggestions'); // Debug
                return;
            }

            // Si l'utilisateur a des jetons et mode jetons sélectionné, suggérer uniquement Dakar
            const liste = (userTokenBalance > 0 && tokenRadio?.checked) ? [dakarRegion] : regionsConnues;
            console.log('Liste des régions suggérées:', liste); // Debug
            const suggestions = liste.filter(r => normaliserTexte(r).includes(valeur));

            console.log('Suggestions filtrées:', suggestions); // Debug

            if (suggestions.length === 0) {
                console.log('Aucune suggestion trouvée pour:', valeur); // Debug
            }

            suggestions.forEach(region => {
                const item = document.createElement('li');
                item.textContent = region;
                item.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                if (userTokenBalance > 0 && region === dakarRegion && tokenRadio?.checked) {
                    item.innerHTML += ' <span class="text-xs text-blue-500 font-medium">💰 Jetons</span>';
                }

                item.addEventListener('mousedown', e => {
                    e.preventDefault();
                    input.value = region;
                    suggestionsBox.innerHTML = '';
                    input.dispatchEvent(new Event('change'));
                    calculerPrix();
                    verifierCompatibiliteJetons();
                });

                suggestionsBox.appendChild(item);
            });
        });

        input.addEventListener('blur', () => setTimeout(() => suggestionsBox.innerHTML = '', 200));
    }

    setupSuggestion('adresse_depart', 'suggestions_depart');
    setupSuggestion('adresse_arrivee', 'suggestions_arrivee');

    // Précisions ville
    function afficherChampPrecision(adresse, boxId, labelId) {
        const region = extraireRegionDepuisAdresse(adresse);
        const box = document.getElementById(boxId);
        const label = document.getElementById(labelId);
        if (region) {
            label.textContent = region;
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }

    function setupPrecisions(inputId, boxId, labelId) {
        const input = document.getElementById(inputId);
        input.addEventListener('input', () => afficherChampPrecision(input.value, boxId, labelId));
        input.addEventListener('change', () => afficherChampPrecision(input.value, boxId, labelId));
        if (input.value) afficherChampPrecision(input.value, boxId, labelId);
    }

    setupPrecisions('adresse_depart', 'precisions_depart_box', 'ville_depart_label');
    setupPrecisions('adresse_arrivee', 'precisions_arrivee_box', 'ville_arrivee_label');

    // Écouteurs
    [departInput, arriveeInput].forEach(i => {
        i.addEventListener('input', () => {
            calculerPrix();
            verifierCompatibiliteJetons();
        });
        i.addEventListener('change', () => {
            calculerPrix();
            verifierCompatibiliteJetons();
        });
    });
    ['type_colis', 'type_livraison'].forEach(name => {
        const el = document.querySelector(`[name="${name}"]`);
        if (el) {
            el.addEventListener('change', calculerPrix);
            el.addEventListener('input', calculerPrix);
        }
    });

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            verifierCompatibiliteJetons();
            calculerPrix();
        });
    });

    // Initialisation
    setTimeout(() => {
        console.log('Initialisation: calcul du prix et vérification des jetons'); // Debug
        calculerPrix();
        verifierCompatibiliteJetons();
    }, 200);
});
</script>

@endsection
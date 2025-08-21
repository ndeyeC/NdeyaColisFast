{{-- resources/views/pages/legal/conditions.blade.php --}}
@extends('layouts.page')

@section('title', $data['title'])

@section('content')
<div class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Conditions d'utilisation</h1>
            <p class="text-gray-600">Dernière mise à jour : {{ $data['lastUpdate'] }}</p>
        </div>

        <div class="prose prose-lg max-w-none">
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Objet</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Les présentes conditions générales d'utilisation (ci-après dénommées « CGU ») ont pour objet de définir les modalités et conditions d'utilisation des services proposés sur le site colisFast (ci-après : le « Service »), ainsi que de définir les droits et obligations des parties dans ce cadre.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Elles sont accessibles et imprimables à tout moment par un lien direct en bas de chaque page du site. Elles peuvent être complétées, le cas échéant, par des conditions d'utilisation particulières à certains services.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Exploitant du service</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Le Service est exploité par la société colisFast SARL, société à responsabilité limitée au capital de XXXXX FCFA, immatriculée au registre du commerce et du crédit mobilier sous le numéro XXXXX, dont le siège social est situé à Dakar, Sénégal.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Acceptation des conditions</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    L'utilisation du Service implique l'acceptation pleine et entière des présentes CGU. Si vous n'acceptez pas d'être lié par les termes des présentes CGU, n'utilisez pas le Service.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Description des services</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    colisFast propose des services de livraison de colis au Sénégal, comprenant notamment :
                </p>
                <ul class="list-disc pl-6 mb-4 text-gray-700">
                    <li>Livraison express (moins de 2 heures)</li>
                    <li>Livraison programmée</li>
                    <li>Solutions e-commerce pour les professionnels</li>
                    <li>Suivi en temps réel des colis</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Inscription et compte utilisateur</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    L'utilisation de certains services nécessite la création d'un compte utilisateur. Lors de votre inscription, vous vous engagez à fournir des informations exactes et à jour.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Vous êtes responsable de la confidentialité de vos identifiants et de toutes les activités qui se déroulent sur votre compte.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Utilisation du service</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Vous vous engagez à utiliser le Service conformément aux présentes CGU et à la réglementation en vigueur. Il vous est notamment interdit :
                </p>
                <ul class="list-disc pl-6 mb-4 text-gray-700">
                    <li>D'expédier des marchandises interdites ou dangereuses</li>
                    <li>De fournir de fausses informations</li>
                    <li>D'utiliser le Service à des fins illégales</li>
                    <li>De porter atteinte aux droits des tiers</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Tarifs et paiement</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Les tarifs des services sont indiqués en francs CFA (FCFA) toutes taxes comprises. Ils sont susceptibles d'être modifiés à tout moment, mais ne s'appliqueront aux commandes déjà passées qu'avec votre accord.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Le paiement peut s'effectuer par les moyens suivants : espèces à la livraison, mobile money, carte bancaire, virement bancaire.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Responsabilité</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    colisFast s'engage à mettre en œuvre tous les moyens nécessaires pour assurer la bonne exécution des services de livraison. Cependant, notre responsabilité est limitée aux dommages directs et prévisibles.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    En cas de perte ou d'endommagement d'un colis, notre responsabilité est limitée à la valeur déclarée du colis, dans la limite de 100 000 FCFA par colis.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Réclamations</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Toute réclamation doit être adressée par écrit dans un délai de 7 jours suivant la livraison ou la date prévue de livraison.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Modification des CGU</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    colisFast se réserve le droit de modifier les présentes CGU à tout moment. Les nouvelles conditions seront applicables dès leur mise en ligne.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Droit applicable</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Les présentes CGU sont soumises au droit sénégalais. Tout litige sera de la compétence des tribunaux de Dakar.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Contact</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pour toute question relative aux présentes CGU, vous pouvez nous contacter :
                </p>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-gray-700 mb-2"><strong>Email :</strong> legal@colisfast.sn</p>
                    <p class="text-gray-700 mb-2"><strong>Téléphone :</strong> +221 XX XXX XX XX</p>
                    <p class="text-gray-700"><strong>Adresse :</strong> Dakar, Sénégal</p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
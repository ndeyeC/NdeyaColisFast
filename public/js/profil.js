document.addEventListener('DOMContentLoaded', function () {
    // Gestion des onglets
    const tabButtons = document.querySelectorAll('.tab-btn');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            let targetTab = null;

            if (btn.innerHTML.includes('Profil') || btn.innerHTML.includes('fa-user')) {
                targetTab = 'profileTab';
            } else if (btn.innerHTML.includes('Accueil') || btn.innerHTML.includes('fa-home')) {
                targetTab = 'homeTab';
            }

            if (targetTab) {
                showTab(targetTab, btn);
            }
        });
    });

    /**
     * Fonction pour afficher un onglet
     */
    function showTab(tabId, clickedBtn) {
        // Masquer tous les onglets
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Afficher l'onglet ciblé
        const targetTab = document.getElementById(tabId);
        if (targetTab) {
            targetTab.classList.remove('hidden');
        }

        // Réinitialiser les boutons
        tabButtons.forEach(btn => {
            btn.classList.remove('active', 'text-red-600');
            btn.classList.add('text-gray-500', 'hover:text-red-600');
        });

        // Marquer le bouton actif
        if (clickedBtn) {
            clickedBtn.classList.remove('text-gray-500');
            clickedBtn.classList.add('active', 'text-red-600');
        }
    }

    /**
     * Gestion des étoiles d'évaluation
     */
    window.updateStars = function (commandeId, rating) {
        const stars = document.querySelectorAll(`.star-${commandeId}`);
        stars.forEach((star, index) => {
            star.classList.remove('text-gray-300', 'text-yellow-400');
            if (index < rating) {
                star.classList.add('text-yellow-400');
            } else {
                star.classList.add('text-gray-300');
            }
        });
    };

    // Onglet home affiché par défaut
    showTab('homeTab');
});

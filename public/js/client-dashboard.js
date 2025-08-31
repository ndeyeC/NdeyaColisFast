class ClientDashboard {
    constructor() {
        this.tabButtons = document.querySelectorAll(".tab-btn");
        this.tabs = document.querySelectorAll(".tab-content");
        this.stars = document.querySelectorAll("#rating span");
        this.refreshInterval = 10000; // 10 secondes
        this.evaluatedCommandes = new Set();
        this.init();
    }

    init() {
        this.setupTabNavigation();
        this.setupRatingSystem();
        this.showTab("homeTab"); // Onglet Accueil affiché par défaut
        this.autoRefresh();
    }

    /**
     * Gestion des onglets (basé sur data-tab)
     */
    setupTabNavigation() {
        this.tabButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                const targetTab = btn.getAttribute("data-tab");
                if (targetTab) {
                    this.showTab(targetTab);
                }
            });
        });
    }

    /**
     * Afficher un onglet et cacher les autres
     */
    showTab(tabId) {
        this.tabs.forEach((tab) => tab.classList.add("hidden"));
        const activeTab = document.getElementById(tabId);
        if (activeTab) {
            activeTab.classList.remove("hidden");
        }
    }

    /**
     * Gestion du système de notation (étoiles)
     */
    setupRatingSystem() {
        this.stars.forEach((star) => {
            star.addEventListener("click", () => {
                const value = star.getAttribute("data-value");
                this.updateStars(value);
            });
        });
    }

    updateStars(value) {
        this.stars.forEach((star) => {
            const starValue = star.getAttribute("data-value");
            star.style.color = starValue <= value ? "gold" : "gray";
        });
    }

    /**
     * Rafraîchissement auto des sections
     */
    autoRefresh() {
        setInterval(() => {
            this.refreshSection("commandes-en-cours", "/client/commandes/en-cours");
            this.refreshSection("historique-commandes", "/client/historique-commandes");
            this.refreshSection("mes-livreurs", "/client/mes-livreurs");
        }, this.refreshInterval);
    }

    async refreshSection(sectionId, url) {
        try {
            const response = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
            const html = await response.text();
            document.getElementById(sectionId).innerHTML = html;

            // Réinitialiser les events après refresh
            this.setupRatingSystem();
            this.setupEvaluationForms();
        } catch (error) {
            console.error("Erreur lors du rafraîchissement :", error);
        }
    }

    /**
     * Système d'évaluation AJAX
     */
    setupEvaluationForms() {
        document.querySelectorAll(".evaluation-form").forEach((form) => {
            form.addEventListener("submit", async (e) => {
                e.preventDefault();

                const commandeId = form.getAttribute("data-commande-id");
                if (this.evaluatedCommandes.has(commandeId)) {
                    alert("Vous avez déjà évalué cette commande !");
                    return;
                }

                const formData = new FormData(form);
                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    });

                    const data = await response.json();
                    if (response.ok) {
                        alert("Évaluation envoyée avec succès !");
                        this.evaluatedCommandes.add(commandeId);
                        form.querySelector("button[type=submit]").disabled = true;
                    } else {
                        alert("Erreur : " + data.message);
                    }
                } catch (error) {
                    console.error("Erreur AJAX :", error);
                    alert("Une erreur est survenue !");
                }
            });
        });
    }
}

// Initialisation après chargement du DOM
document.addEventListener("DOMContentLoaded", () => new ClientDashboard());

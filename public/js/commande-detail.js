document.addEventListener("DOMContentLoaded", function() {
    const confirmBtn = document.getElementById("confirm-btn");

    if (confirmBtn) {
        confirmBtn.addEventListener("click", function() {
            confirmBtn.disabled = true;
            confirmBtn.innerText = "Confirmation en cours...";

            fetch(confirmBtn.dataset.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": confirmBtn.dataset.csrf,
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Met à jour le statut affiché
                    const statusSpan = document.querySelector("#status-span");
                    if (statusSpan) {
                        statusSpan.innerText = "Confirmée";
                        statusSpan.className = "px-3 py-1 rounded-full bg-green-600 text-white";
                    }

                    // Supprime le bouton
                    confirmBtn.remove();

                    // Petite notif
                    alert(data.message);
                } else {
                    alert(data.message);
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = "Confirmer la commande";
                }
            })
            .catch(err => {
                console.error(err);
                alert("Erreur serveur");
                confirmBtn.disabled = false;
                confirmBtn.innerText = "Confirmer la commande";
            });
        });
    }
});

let isAccepting = false;

function accepterCommande(commandeId) {
    if (isAccepting) return;
    if (!confirm('Êtes-vous sûr de vouloir accepter cette commande ?')) return;
    isAccepting = true;

    const btn = event.target.closest('.accepter-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Acceptation...';
    btn.disabled = true;

    // CORRECTION: S'assurer que la méthode POST est bien utilisée
    fetch(`/livreur/commandes/${commandeId}/accepter`, {
        method: 'POST', // Explicitement POST
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json' // Ajouter Accept header
        },
        // Ajouter un body même si vide pour s'assurer que c'est une requête POST valide
        body: JSON.stringify({})
    })
    .then(res => {
        // Vérifier si la réponse est OK avant de parser JSON
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Acceptée';
            btn.classList.remove('bg-green-500', 'hover:bg-green-600');
            btn.classList.add('bg-gray-500', 'cursor-not-allowed');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert(data.message || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        console.error('Erreur complète:', error);
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Erreur réseau: ' + error.message);
    })
    .finally(() => isAccepting = false);
}

function voirDetails(commandeId) {
    fetch(`/livreur/commandes/${commandeId}`, {
        method: 'GET', // Cette route est bien GET
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('detailsContent').innerHTML = `
                <p><strong>Référence :</strong> ${data.commande.reference}</p>
                <p><strong>Départ :</strong> ${data.commande.adresse_depart}</p>
                <p><strong>Arrivée :</strong> ${data.commande.adresse_arrivee}</p>
                <p><strong>Type colis :</strong> ${data.commande.type_colis}</p>
                <p><strong>Prix :</strong> ${data.commande.prix_final} FCFA</p>
                <p><strong>Client :</strong> ${data.commande.user.name} (${data.commande.user.numero_telephone})</p>
            `;
            document.getElementById('detailsModal').classList.remove('hidden');
        } else {
            alert('Impossible de charger les détails');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du chargement des détails: ' + error.message);
    });
}

function fermerModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}
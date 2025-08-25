let isAccepting = false;

function accepterCommande(commandeId) {
    if (isAccepting) return;
    if (!confirm('Êtes-vous sûr de vouloir accepter cette commande ?')) return;
    isAccepting = true;

    const btn = event.target.closest('.accepter-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Acceptation...';
    btn.disabled = true;

    fetch(`/livreur/commandes/${commandeId}/accepter`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(res => res.json())
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
    .catch(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Erreur réseau');
    })
    .finally(() => isAccepting = false);
}

function voirDetails(commandeId) {
    fetch(`/livreur/commandes/${commandeId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
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
        console.error(error);
        alert('Erreur lors du chargement des détails');
    });
}

function fermerModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}


function fermerModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}
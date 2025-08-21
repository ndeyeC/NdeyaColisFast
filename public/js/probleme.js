// Filtrage par onglets
document.querySelectorAll('[data-filter]').forEach(button => {
    button.addEventListener('click', function() {
        // Mettre à jour les onglets actifs
        document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const items = document.querySelectorAll('.probleme-item');
        
        items.forEach(item => {
            if (filter === 'all' || item.dataset.status === filter) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Traiter un problème
function traiterProbleme(livraisonId) {
  // Mettre l'URL POST correcte dans l'attribut action du formulaire
  document.getElementById('formTraiterProbleme').action = `/admin/livraisons/${livraisonId}/resoudre-probleme`;
  
  // Ouvrir le modal Bootstrap
  var modal = new bootstrap.Modal(document.getElementById('modalTraiterProbleme'));
  modal.show();
}


// Gestion de l'affichage du champ nouveau livreur
document.querySelector('select[name="action"]').addEventListener('change', function() {
    const divNouveauLivreur = document.getElementById('divNouveauLivreurModal');
    const selectLivreur = document.querySelector('select[name="nouveau_driver_id"]');
    
    if (this.value === 'reassigner') {
        divNouveauLivreur.style.display = 'block';
        selectLivreur.required = true;
    } else {
        divNouveauLivreur.style.display = 'none';
        selectLivreur.required = false;
    }
});

// Agrandir une image
function agrandirImage(src) {
    document.getElementById('imageAgrandie').src = src;
    new bootstrap.Modal(document.getElementById('modalImage')).show();
}

// Auto-refresh de la page toutes les 2 minutes pour les nouveaux problèmes
setInterval(function() {
    if (document.querySelector('[data-filter="en_attente"].active')) {
        location.reload();
    }
}, 120000); // 2 minutes

// Notification sonore pour les nouveaux problèmes (optionnel)
function jouerSonNotification() {
    // Vous pouvez ajouter un son de notification ici
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Nouveau problème signalé', {
            body: 'Un livreur a signalé un problème sur une livraison',
            icon: '/favicon.ico'
        });
    }
}

// Demander permission pour les notifications
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
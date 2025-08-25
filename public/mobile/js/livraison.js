document.getElementById('filtres-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    window.location.href = window.location.pathname + '?' + params.toString();
});

function ouvrirModalProbleme(livraisonId) {
    fetch(`/admin/livraisons/${livraisonId}`)
        .then(response => response.json())
        .then(data => {
            if (data.probleme) {
                document.getElementById('detailsProbleme').innerHTML = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Problème signalé</h6>
                        <p><strong>Type:</strong> ${data.probleme.type_probleme.replace('_', ' ')}</p>
                        <p><strong>Description:</strong> ${data.probleme.description}</p>
                        <p><strong>Signalé le:</strong> ${new Date(data.probleme.date_signalement).toLocaleString()}</p>
                        <p><strong>Livreur:</strong> ${data.livreur_name}</p>
                        ${data.probleme.photo ? `<img src="/storage/${data.probleme.photo}" class="img-fluid mt-2" style="max-height: 200px;">` : ''}
                    </div>
                `;
                
                document.getElementById('formProbleme').action = `/admin/livraisons/${livraisonId}/resoudre-probleme`;
                new bootstrap.Modal(document.getElementById('modalProbleme')).show();
            }
        });
}

// Afficher/masquer le champ nouveau livreur
document.querySelector('select[name="action"]').addEventListener('change', function() {
    const divNouveauLivreur = document.getElementById('divNouveauLivreur');
    if (this.value === 'reassigner') {
        divNouveauLivreur.style.display = 'block';
        document.querySelector('select[name="nouveau_driver_id"]').required = true;
    } else {
        divNouveauLivreur.style.display = 'none';
        document.querySelector('select[name="nouveau_driver_id"]').required = false;
    }
});

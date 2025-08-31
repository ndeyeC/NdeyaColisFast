document.addEventListener('DOMContentLoaded', function() {
    // Récupération des données depuis les attributs data
    const moisLabels = JSON.parse(document.getElementById('chart-data').dataset.moisLabels);
    const livraisonData = JSON.parse(document.getElementById('chart-data').dataset.livraisonData);
    const revenusData = JSON.parse(document.getElementById('chart-data').dataset.revenusData);
    const statutsLabels = JSON.parse(document.getElementById('chart-data').dataset.statutsLabels);
    const statutsData = JSON.parse(document.getElementById('chart-data').dataset.statutsData);

    // Graphique des livraisons par mois
    new Chart(document.getElementById('livraisonsChart'), {
        type: 'bar',
        data: {
            labels: moisLabels,
            datasets: [{
                label: 'Livraisons',
                data: livraisonData,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 5, precision: 0 }
                }
            }
        }
    });

    // Graphique des revenus mensuels
    new Chart(document.getElementById('revenusChart'), {
        type: 'line',
        data: {
            labels: moisLabels,
            datasets: [{
                label: 'Revenus CFA',
                data: revenusData,
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.raw.toLocaleString() + ' CFA'
                    }
                }
            },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Graphique des statuts des livraisons
    new Chart(document.getElementById('statutsChart'), {
        type: 'doughnut',
        data: {
            labels: statutsLabels,
            datasets: [{
                data: statutsData,
                backgroundColor: [
                    '#0d6efd', '#ffc107', '#28a745',
                    '#dc3545', '#6c757d', '#6610f2'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
});
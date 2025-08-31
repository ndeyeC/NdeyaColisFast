document.addEventListener('DOMContentLoaded', function () {

    fetch(revenusGraphUrl)
    .then(response => response.json())
    .then(data => {
            //  Graphique revenus journaliers
            new Chart(document.getElementById('revenusChart'), {
                type: 'line',
                data: {
                    labels: data.dates,
                    datasets: [{
                        label: "Revenus journaliers",
                        data: data.revenus_journaliers,
                        borderColor: "#4e73df",
                        backgroundColor: "rgba(78, 115, 223, 0.1)",
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            // ✅ Graphique répartition des types de livraisons
            new Chart(document.getElementById('typesChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data.repartition_types),
                    datasets: [{
                        data: Object.values(data.repartition_types),
                        backgroundColor: ["#4e73df", "#1cc88a", "#36b9cc"]
                    }]
                },
                options: { responsive: true }
            });
        })
        .catch(() => console.error("Erreur lors du chargement des données du graphique"));

});
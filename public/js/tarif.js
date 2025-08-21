$(document).ready(function() {
    // Filtrage en temps réel
    $('#searchInput, #zoneFilter, #typeFilter, #zoneTypeFilter').on('keyup change', function() {
        const search = $('#searchInput').val().toLowerCase();
        const zone = $('#zoneFilter').val();
        const type = $('#typeFilter').val();
        const typeZone = $('#zoneTypeFilter').val();

        $('.tarif-row').each(function() {
            const rowZone = $(this).data('zone');
            const rowType = $(this).data('type');
            const rowTypeZone = $(this).data('typezone');
            const rowText = $(this).text().toLowerCase();

            const zoneMatch = zone === '' || rowZone === zone;
            const typeMatch = type === '' || rowType === type;
            const typeZoneMatch = typeZone === '' || rowTypeZone === typeZone;
            const searchMatch = rowText.includes(search);

            $(this).toggle(zoneMatch && typeMatch && typeZoneMatch && searchMatch);
        });
    });

    // Réinitialisation des filtres
    $('#resetFilters').click(function() {
        $('#searchInput').val('');
        $('#zoneFilter').val('');
        $('#typeFilter').val('');
        $('#zoneTypeFilter').val('');
        $('.tarif-row').show();
    });
});
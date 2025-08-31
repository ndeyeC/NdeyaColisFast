document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const clientFields = document.querySelectorAll('.client-field');
    const livreurFields = document.querySelectorAll('.livreur-field');

    function toggleFields() {
        const selectedRole = roleSelect.value;
        const isClient = selectedRole === 'client';
        const isLivreur = selectedRole === 'livreur';

        clientFields.forEach(el => el.style.display = isClient ? 'block' : 'none');
        livreurFields.forEach(el => el.style.display = isLivreur ? 'block' : 'none');
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', toggleFields);
        toggleFields(); // afficher correctement au chargement
    }
});

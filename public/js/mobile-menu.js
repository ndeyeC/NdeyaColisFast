// Toggle mobile sidebar
function toggleMobileMenu() {
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('mobile-menu-overlay');
    const button = document.getElementById('mobile-menu-button');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        button.innerHTML = '<i class="fas fa-times text-xl"></i>';
        document.body.style.overflow = 'hidden';
    } else {
        closeMobileMenu();
    }
}

function closeMobileMenu() {
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('mobile-menu-overlay');
    const button = document.getElementById('mobile-menu-button');
    
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    button.innerHTML = '<i class="fas fa-bars text-xl"></i>';
    document.body.style.overflow = '';
}

// Fermer le menu si on redimensionne l'écran
window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) closeMobileMenu();
});

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenuBtn = document.getElementById('close-menu');
    const mobileOverlay = document.getElementById('mobile-overlay');
    const hamburgerIcon = mobileMenuBtn.querySelector('i');
    const body = document.body;

    function openMenu() {
        mobileMenu.classList.add('active');
        mobileOverlay.classList.add('active');
        mobileMenuBtn.classList.add('active');
        hamburgerIcon.classList.remove('fa-bars');
        hamburgerIcon.classList.add('fa-times');
        body.style.overflow = 'hidden'; 
    }

    function closeMenu() {
        mobileMenu.classList.remove('active');
        mobileOverlay.classList.remove('active');
        mobileMenuBtn.classList.remove('active');
        hamburgerIcon.classList.remove('fa-times');
        hamburgerIcon.classList.add('fa-bars');
        body.style.overflow = ''; 
    }

    // Event listeners
    mobileMenuBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        mobileMenu.classList.contains('active') ? closeMenu() : openMenu();
    });

    closeMenuBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeMenu();
    });

    mobileOverlay.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMenu();
        }
    });

    const mobileMenuLinks = document.querySelectorAll('.mobile-menu-link');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(closeMenu, 300);
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768 && mobileMenu.classList.contains('active')) {
            closeMenu();
        }
    });
});

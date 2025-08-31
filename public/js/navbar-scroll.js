window.addEventListener('scroll', () => {
    const nav = document.querySelector('nav');
    if (window.scrollY > 50) {
        nav.classList.add('shadow-2xl');
        nav.classList.remove('shadow-lg');
    } else {
        nav.classList.remove('shadow-2xl');
        nav.classList.add('shadow-lg');
    }
});

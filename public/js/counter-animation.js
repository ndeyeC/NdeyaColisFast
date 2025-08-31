const animateCounters = () => {
    const counters = document.querySelectorAll('.text-4xl.font-black');
    counters.forEach(counter => {
        const target = counter.textContent;
        const numericValue = parseInt(target.replace(/\D/g, ''));
        if (numericValue && numericValue > 0) {
            let current = 0;
            const increment = numericValue / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= numericValue) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    if (target.includes('min')) counter.textContent = Math.floor(current) + 'min';
                    else if (target.includes('+')) counter.textContent = Math.floor(current) + '+';
                    else if (target.includes('/')) counter.textContent = (current / 10).toFixed(1) + '/5';
                    else counter.textContent = Math.floor(current);
                }
            }, 30);
        }
    });
};

// Observer pour lancer les compteurs seulement quand visible
const statsSection = document.querySelector('.relative.-mt-20');
if (statsSection) {
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                statsObserver.unobserve(entry.target);
            }
        });
    });
    statsObserver.observe(statsSection);
}

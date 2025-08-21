 // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenuBtn = document.getElementById('close-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        closeMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
        
        // Close menu when clicking outside
        mobileMenu.addEventListener('click', (e) => {
            if (e.target === mobileMenu) {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // Navbar scroll effect
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
        
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.card-hover').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
        
        // Counter animation for stats
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
                            if (target.includes('min')) {
                                counter.textContent = Math.floor(current) + 'min';
                            } else if (target.includes('+')) {
                                counter.textContent = Math.floor(current) + '+';
                            } else if (target.includes('/')) {
                                counter.textContent = (current / 10).toFixed(1) + '/5';
                            } else {
                                counter.textContent = Math.floor(current);
                            }
                        }
                    }, 30);
                }
            });
        };
        
        // Trigger counter animation when stats section is visible
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
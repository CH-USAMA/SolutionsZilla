// ===== SCROLL ANIMATIONS =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

// Observe all animated elements
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.fade-up, .fade-up-delay, .fade-up-delay-1, .fade-up-delay-2, .fade-up-delay-3');
    animatedElements.forEach(el => observer.observe(el));
});

// ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ===== WHATSAPP BUTTON TRACKING =====
const whatsappButtons = document.querySelectorAll('a[href*="wa.me"]');
whatsappButtons.forEach(button => {
    button.addEventListener('click', () => {
        console.log('WhatsApp CTA clicked');
        // Add analytics tracking here if needed
    });
});

// ===== PARALLAX EFFECT FOR HERO MOCKUPS =====
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const mockupWrapper = document.querySelector('.mockup-wrapper');
    
    if (mockupWrapper && scrolled < window.innerHeight) {
        mockupWrapper.style.transform = `translateY(${scrolled * 0.3}px)`;
    }
});

// ===== ADD HOVER GLOW EFFECT TO CARDS =====
const cards = document.querySelectorAll('.problem-card, .solution-card, .audience-item');
cards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.5), 0 0 24px rgba(6, 182, 212, 0.2)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.boxShadow = '';
    });
});

// ===== TYPING ANIMATION FOR WHATSAPP MESSAGE (Optional Enhancement) =====
const waMessage = document.querySelector('.wa-message');
if (waMessage) {
    const messageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                waMessage.style.animation = 'fadeUp 0.6s ease-out forwards';
            }
        });
    }, { threshold: 0.5 });
    
    messageObserver.observe(waMessage);
}

// ===== PERFORMANCE: REDUCE ANIMATIONS ON LOW-END DEVICES =====
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
if (prefersReducedMotion.matches) {
    document.querySelectorAll('.floating').forEach(el => {
        el.style.animation = 'none';
    });
}

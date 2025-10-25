class Carrossel {
    constructor(container) {
        this.container = container;
        this.slides = container.querySelectorAll('.slide-destaque');
        this.currentSlide = 0;
        this.interval = null;
        this.init();
    }
    
    init() {
        // Mostrar primeiro slide
        this.showSlide(0);
        
        // Iniciar autoplay
        this.startAutoplay();
        
        // Pausar ao passar mouse
        this.container.addEventListener('mouseenter', () => {
            this.stopAutoplay();
        });
        
        this.container.addEventListener('mouseleave', () => {
            this.startAutoplay();
        });
    }
    
    showSlide(index) {
        // Esconder todos os slides
        this.slides.forEach(slide => {
            slide.classList.remove('ativo');
        });
        
        // Mostrar slide atual
        this.slides[index].classList.add('ativo');
        this.currentSlide = index;
    }
    
    nextSlide() {
        let next = this.currentSlide + 1;
        if (next >= this.slides.length) {
            next = 0;
        }
        this.showSlide(next);
    }
    
    startAutoplay() {
        this.interval = setInterval(() => {
            this.nextSlide();
        }, 5000); // Mudar a cada 5 segundos
    }
    
    stopAutoplay() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    }
}

// Inicializar carrossel quando DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    const carrosselContainer = document.querySelector('.carrossel-destaques');
    if (carrosselContainer) {
        new Carrossel(carrosselContainer);
    }
});
class MenuMobile {
    constructor() {
        this.menu = document.querySelector('.menu-principal');
        this.toggleBtn = document.querySelector('.menu-toggle');
        this.overlay = document.querySelector('.menu-overlay');
        this.closeBtn = document.querySelector('.menu-close');
        
        this.init();
    }
    
    init() {
        this.addEventListeners();
    }
    
    addEventListeners() {
        // Botão abrir menu
        this.toggleBtn.addEventListener('click', () => this.openMenu());
        
        // Botão fechar menu
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.closeMenu());
        }
        
        // Overlay para fechar
        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.closeMenu());
        }
        
        // Fechar menu ao clicar em um link
        if (this.menu) {
            this.menu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => this.closeMenu());
            });
        }
        
        // Fechar menu com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closeMenu();
        });
    }
    
    openMenu() {
        if (this.menu) this.menu.classList.add('active');
        if (this.overlay) this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    closeMenu() {
        if (this.menu) this.menu.classList.remove('active');
        if (this.overlay) this.overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Inicializar quando DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    new MenuMobile();
});
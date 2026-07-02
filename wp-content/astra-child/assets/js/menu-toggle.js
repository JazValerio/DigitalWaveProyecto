/**
 * Lógica del Menú Responsivo
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.querySelector('.menu-toggle-btn');
        const menu = document.getElementById('site-navigation');

        if (!button || !menu) return;

        button.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Alternar atributos de accesibilidad y clases
            this.setAttribute('aria-expanded', !isExpanded);
            this.classList.toggle('is-active');
            menu.classList.toggle('is-active');
        });

        // Cerrar menú al hacer click en un enlace
        const links = menu.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                button.setAttribute('aria-expanded', 'false');
                button.classList.remove('is-active');
                menu.classList.remove('is-active');
            });
        });
    });
})();

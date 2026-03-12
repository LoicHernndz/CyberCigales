/**
 * Scripts globaux CyberCigales
 * Chargé sur toutes les pages via le footer commun.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Toggle du menu mobile (hamburger)
    var toggleButton = document.querySelector('.mobile-menu-toggle');
    var nav = document.querySelector('.main-nav');

    if (toggleButton && nav) {
        toggleButton.addEventListener('click', function () {
            nav.classList.toggle('active');
            toggleButton.classList.toggle('active');
        });
    }

    // Gestion des boutons avec confirmation (data-confirm)
    document.querySelectorAll('[data-confirm]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
});

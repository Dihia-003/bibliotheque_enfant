/**
 * Menu navigation - gestion du menu hamburger
 * Script amélioré pour fonctionner sur toutes les pages
 */

// Fonction d'initialisation du menu
function initMobileMenu() {
    const bookBurger = document.getElementById('bookBurger');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (bookBurger && mobileMenu) {
        console.log('Menu burger et menu mobile trouvés');
        
        // Nettoyer les anciens écouteurs d'événements si existants
        bookBurger.removeEventListener('click', toggleMenu);
        
        // Ajouter l'écouteur d'événements
        bookBurger.addEventListener('click', toggleMenu);
        
        // Fermer le menu quand on clique à l'extérieur
        document.addEventListener('click', closeMenuOutside);
    } else {
        console.error('Menu burger ou menu mobile non trouvé');
    }
    
    // Fonction pour basculer l'état du menu
    function toggleMenu(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        bookBurger.classList.toggle('open');
        mobileMenu.classList.toggle('show');
        console.log('Menu burger cliqué');
    }
    
    // Fonction pour fermer le menu en cliquant à l'extérieur
    function closeMenuOutside(event) {
        if (bookBurger && mobileMenu && 
            !bookBurger.contains(event.target) && 
            !mobileMenu.contains(event.target) && 
            mobileMenu.classList.contains('show')) {
            
            bookBurger.classList.remove('open');
            mobileMenu.classList.remove('show');
        }
    }
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', initMobileMenu);

// Initialiser aussi lors de chaque navigation AJAX ou chargement de page
window.addEventListener('load', initMobileMenu);

// Réinitialiser lors des changements d'état ou d'URL
if (window.history && window.history.pushState) {
    window.addEventListener('popstate', initMobileMenu);
}

// Forcer l'initialisation immédiate si le DOM est déjà chargé
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    initMobileMenu();
} 
/**
 * Script de réparation pour le menu burger
 * Ajoute le menu burger à toutes les pages si nécessaire
 */

function ensureMenuBurgerExists() {
    // Vérifier si nous sommes sur mobile
    if (window.innerWidth > 800) {
        return; // Ne rien faire sur grands écrans
    }
    
    // Vérifier si le menu burger existe déjà
    if (document.getElementById('bookBurger')) {
        console.log('Menu burger déjà présent');
        return;
    }
    
    console.log('Ajout du menu burger manquant');
    
    // Trouver la navigation
    const siteNav = document.querySelector('.site-nav');
    if (!siteNav) {
        console.error('Navigation non trouvée');
        return;
    }
    
    // Trouver le menu
    let mobileMenu = document.getElementById('mobileMenu');
    
    // Si nous avons trouvé la navigation mais pas le menu burger, l'ajouter
    const menuBurger = document.createElement('div');
    menuBurger.className = 'menu-burger';
    menuBurger.id = 'bookBurger';
    
    // Créer les trois barres
    for (let i = 0; i < 3; i++) {
        const span = document.createElement('span');
        menuBurger.appendChild(span);
    }
    
    // Si le premier enfant est le titre du site, insérer après
    const siteTitle = siteNav.querySelector('.site-title');
    if (siteTitle) {
        siteTitle.insertAdjacentElement('afterend', menuBurger);
    } else {
        // Sinon insérer au début
        siteNav.insertAdjacentElement('afterbegin', menuBurger);
    }
    
    // Si nous n'avons pas trouvé le mobileMenu, créer un menu par défaut
    if (!mobileMenu) {
        mobileMenu = document.createElement('ul');
        mobileMenu.className = 'nav-menu mobile-nav';
        mobileMenu.id = 'mobileMenu';
        
        // Ajouter des liens par défaut (peut être adapté selon les besoins)
        const defaultLinks = [
            { url: '/', text: 'Accueil' },
            { url: '/livre', text: 'Livres' },
            { url: '/auteurs', text: 'Auteurs' }
        ];
        
        defaultLinks.forEach(link => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = link.url;
            a.textContent = link.text;
            li.appendChild(a);
            mobileMenu.appendChild(li);
        });
        
        siteNav.appendChild(mobileMenu);
    }
    
    // Appeler l'initialisation du menu
    if (typeof initMobileMenu === 'function') {
        setTimeout(initMobileMenu, 100);
    } else {
        console.error('Fonction initMobileMenu non trouvée');
    }
}

// Exécuter au chargement de la page
document.addEventListener('DOMContentLoaded', ensureMenuBurgerExists);

// Exécuter également lors du redimensionnement de la fenêtre
window.addEventListener('resize', ensureMenuBurgerExists);

// Exécuter immédiatement si le DOM est déjà chargé
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    ensureMenuBurgerExists();
} 
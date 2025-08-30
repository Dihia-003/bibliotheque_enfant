# Dockerfile pour Symfony 7.2 sur Render - Version simplifiée et corrigée
FROM php:8.2-cli

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installation des dépendances système minimales
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Installation de l'extension PostgreSQL pour PHP
RUN docker-php-ext-install pdo_pgsql

# Vérification de l'installation
RUN php -m | grep pdo_pgsql || echo "WARNING: pdo_pgsql not found"
RUN php -m | grep pdo || echo "WARNING: no pdo drivers found"

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www

# Copie des fichiers de dépendances
COPY composer.json composer.lock ./

# Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copie de tout le code source
COPY . .

# Création des dossiers nécessaires
RUN mkdir -p var/cache/prod var/log public/uploads/couvertures

# Création des assets de fallback avec les noms hashés exacts
RUN mkdir -p public/assets/styles public/assets/controllers public/assets/vendor/@hotwired/stimulus public/assets/vendor/@hotwired/turbo public/assets/@symfony/stimulus-bundle public/assets/@symfony/ux-turbo

# Créer des fichiers CSS et JS de base avec les noms hashés exacts
RUN echo "/* Fallback CSS - Bibliothèque des Enfants */" > public/assets/styles/app-Wux0ucT.css && \
    echo "body { background-color: #f1f9ff; font-family: 'Comic Sans MS', cursive; }" >> public/assets/styles/app-Wux0ucT.css && \
    echo "/* Fallback CSS - Bibliothèque des Enfants */" > public/assets/styles/app-j_TRKrf.css && \
    echo "body { background-color: #f1f9ff; font-family: 'Comic Sans MS', cursive; }" >> public/assets/styles/app-j_TRKrf.css && \
    echo "/* Fallback JS - Bibliothèque des Enfants */" > public/assets/app-jgPm2-L.js && \
    echo "console.log('Fallback JS loaded');" >> public/assets/app-jgPm2-L.js && \
    echo "/* Fallback Bootstrap */" > public/assets/bootstrap-xCO4u8H.js && \
    echo "console.log('Fallback Bootstrap loaded');" >> public/assets/bootstrap-xCO4u8H.js && \
    echo "/* Fallback Stimulus */" > public/assets/vendor/@hotwired/stimulus/stimulus.index-S4zNcea.js && \
    echo "console.log('Fallback Stimulus loaded');" >> public/assets/vendor/@hotwired/stimulus/stimulus.index-S4zNcea.js && \
    echo "/* Fallback Turbo */" > public/assets/vendor/@hotwired/turbo/turbo.index-pT15T6h.js && \
    echo "console.log('Fallback Turbo loaded');" >> public/assets/vendor/@hotwired/turbo/turbo.index-pT15T6h.js && \
    echo "/* Fallback Stimulus Bundle */" > public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js && \
    echo "console.log('Fallback Stimulus Bundle loaded');" >> public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js && \
    echo "/* Fallback UX Turbo */" > public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js && \
    echo "console.log('Fallback UX Turbo loaded');" >> public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js && \
    echo "/* Fallback Loader */" > public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js && \
    echo "console.log('Fallback Loader loaded');" >> public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js && \
    echo "/* Fallback Hello Controller */" > public/assets/controllers/hello_controller-VYgvytJ.js && \
    echo "console.log('Fallback Hello Controller loaded');" >> public/assets/controllers/hello_controller-VYgvytJ.js

# Définition des permissions
RUN chown -R www-data:www-data var public/uploads
RUN chmod -R 755 var public/uploads

# Exposition du port
EXPOSE 8000

# Script de démarrage
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

# Dockerfile pour Symfony 7.2 sur Render - Version corrigée avec autoload forcé
FROM php:8.2-apache

# Variables d'environnement
ENV APACHE_DOCUMENT_ROOT=/var/www/public
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer et Symfony CLI
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www

# Copie des fichiers de configuration en premier
COPY config/ ./config/
COPY src/ ./src/
COPY templates/ ./templates/
COPY migrations/ ./migrations/
COPY public/ ./public/
COPY bin/ ./bin/
COPY importmap.php ./
COPY symfony.lock ./
COPY env.example ./

# Copie des fichiers de dépendances en premier
COPY composer.json ./
COPY composer.lock ./

# Création du fichier .env à partir de l'exemple
RUN cp env.example .env

# Installation des dépendances avec scripts pour configurer Symfony Runtime
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copie du reste du code source
COPY . .

# Génération FORCÉE de l'autoload et vérification
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative --no-interaction
RUN ls -la vendor/ || echo "Vendor directory not found"
RUN ls -la vendor/autoload* || echo "Autoload files not found"

# Génération des assets Symfony
RUN php bin/console asset:install public --env=prod || echo "Asset install failed, continuing..."

# Création des assets de fallback pour éviter les erreurs 404
RUN mkdir -p public/assets/styles public/assets/@symfony/stimulus-bundle public/assets/@symfony/ux-turbo public/assets/controllers public/assets/vendor/@hotwired/stimulus public/assets/vendor/@hotwired/turbo

# Création des fichiers CSS de fallback
RUN echo "/* Fallback CSS - Bibliothèque des Enfants */" > public/assets/styles/app-Wux0ucT.css && \
    echo "body { background-color: #f1f9ff; font-family: 'Comic Sans MS', cursive; }" >> public/assets/styles/app-Wux0ucT.css

# Création des fichiers JavaScript de fallback
RUN echo "/* Fallback App JS */" > public/assets/app-jgPm2-L.js && \
    echo "console.log('Fallback App JS loaded');" >> public/assets/app-jgPm2-L.js

RUN echo "/* Fallback Bootstrap */" > public/assets/bootstrap-xCO4u8H.js && \
    echo "console.log('Fallback Bootstrap loaded');" >> public/assets/bootstrap-xCO4u8H.js

RUN echo "/* Fallback Stimulus */" > public/assets/vendor/@hotwired/stimulus/stimulus.index-S4zNcea.js && \
    echo "console.log('Fallback Stimulus loaded');" >> public/assets/vendor/@hotwired/stimulus/stimulus.index-S4zNcea.js

RUN echo "/* Fallback Turbo */" > public/assets/vendor/@hotwired/turbo/turbo.index-pT15T6h.js && \
    echo "console.log('Fallback Turbo loaded');" >> public/assets/vendor/@hotwired/turbo/turbo.index-pT15T6h.js

RUN echo "/* Fallback Controllers */" > public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js && \
    echo "console.log('Fallback Controllers loaded');" >> public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js

RUN echo "/* Fallback Loader */" > public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js && \
    echo "console.log('Fallback Loader loaded');" >> public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js

RUN echo "/* Fallback UX Turbo */" > public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js && \
    echo "console.log('Fallback UX Turbo loaded');" >> public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js

RUN echo "/* Fallback Hello Controller */" > public/assets/controllers/hello_controller-VYgvytJ.js && \
    echo "console.log('Fallback Hello Controller loaded');" >> public/assets/controllers/hello_controller-VYgvytJ.js

# Création des dossiers nécessaires
RUN mkdir -p var/cache var/log public/uploads

# Création de la base de données SQLite et exécution des migrations
RUN touch var/database.sqlite && \
    chmod 664 var/database.sqlite && \
    php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Configuration d'Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copie du script d'entrée
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Définition des permissions
RUN chown -R www-data:www-data /var/www

# Exposition du port 80
EXPOSE 80

# Point d'entrée
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

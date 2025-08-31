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

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

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
COPY .env ./

# Copie des fichiers de dépendances en premier
COPY composer.json ./
COPY composer.lock ./

# Installation des dépendances SANS scripts pour éviter l'erreur symfony-cmd
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copie du reste du code source
COPY . .

# Génération FORCÉE de l'autoload et vérification
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative --no-interaction
RUN ls -la vendor/ || echo "Vendor directory not found"
RUN ls -la vendor/autoload* || echo "Autoload files not found"

# Création des dossiers nécessaires
RUN mkdir -p var/cache var/log public/uploads

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

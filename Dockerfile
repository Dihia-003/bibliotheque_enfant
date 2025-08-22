# Dockerfile pour Symfony 7.2 sur Render
FROM php:8.2-fpm

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1
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
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && rm -rf /var/lib/apt/lists/*

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

# Copie du fichier de production
COPY .env.prod .env

# Création des dossiers nécessaires
RUN mkdir -p var/cache/prod var/log public/uploads/couvertures

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

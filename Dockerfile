# Utilisation de l'image PHP 8.2 avec Apache
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
    && a2enmod rewrite

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www

# Copie des fichiers de dépendances
COPY composer.json composer.lock ./

# Installation des dépendances (sans scripts)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copie du reste du code source
COPY . .

# Création des dossiers nécessaires
RUN mkdir -p var/cache var/log public/uploads

# Configuration d'Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Définition des permissions
RUN chown -R www-data:www-data /var/www

# Exposition du port 80
EXPOSE 80

# Point d'entrée ultra-simple
CMD ["apache2-foreground"]

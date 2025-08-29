# Dockerfile pour Symfony 7.2 sur Render - Version ultra-minimale
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

# Création du fichier .env directement
RUN echo 'APP_ENV=prod' > .env && \
    echo 'APP_DEBUG=0' >> .env && \
    echo 'APP_SECRET=your_production_secret_here' >> .env && \
    echo 'DATABASE_URL="postgresql://postgres.hyrbvsxrxfanivasssfl:Tbrmdr-213@aws-0-eu-central-1.pooler.supabase.com:6543/postgres"' >> .env

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

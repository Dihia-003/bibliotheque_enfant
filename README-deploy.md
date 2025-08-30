# Guide de déploiement du site de bibliothèque

## Prérequis
- PHP 8.2 ou supérieur
- MySQL 8.0 ou supérieur
- Composer
- Serveur web avec mod_rewrite activé (Apache) ou configuration équivalente (Nginx)

## Étapes de déploiement

### 1. Configuration

Créez un fichier `.env.local` sur votre serveur avec les informations suivantes:

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=votre_secret_unique
DATABASE_URL="mysql://utilisateur:mot_de_passe@hote:port/bibliotheque?serverVersion=8.0.32&charset=utf8mb4"
```

Remplacez:
- `votre_secret_unique` par une chaîne aléatoire (vous pouvez en générer une avec `openssl rand -hex 16`)
- `utilisateur`, `mot_de_passe`, `hote` et `port` par les informations de connexion à votre base de données

### 2. Installation des dépendances

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Préparation de la base de données

Créez une base de données `bibliotheque` sur votre serveur MySQL, puis exécutez:

```bash
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
```

### 4. Compilation des assets

```bash
php bin/console asset-map:compile
```

### 5. Nettoyage du cache

```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

### 6. Configuration du serveur web

#### Pour Apache

Le fichier `.htaccess` est déjà configuré dans le dossier `public/`. Assurez-vous que votre DocumentRoot pointe vers ce dossier.

Exemple de configuration VirtualHost:
```apache
<VirtualHost *:80>
    ServerName votre-site.com
    ServerAlias www.votre-site.com
    
    DocumentRoot /chemin/vers/votre/projet/public
    
    <Directory /chemin/vers/votre/projet/public>
        AllowOverride All
        Require all granted
        
        FallbackResource /index.php
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/votre-site_error.log
    CustomLog ${APACHE_LOG_DIR}/votre-site_access.log combined
</VirtualHost>
```

#### Pour Nginx

Créez une configuration comme suit:

```nginx
server {
    listen 80;
    server_name votre-site.com www.votre-site.com;
    root /chemin/vers/votre/projet/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/votre-site_error.log;
    access_log /var/log/nginx/votre-site_access.log;
}
```

### 7. Permissions

Assurez-vous que les dossiers suivants sont accessibles en écriture par le serveur web:
- `var/`
- `public/uploads/`

```bash
chmod -R 775 var/
chmod -R 775 public/uploads/
chown -R www-data:www-data var/ public/uploads/
```

## Hébergeurs spécifiques

### OVH

Ajoutez un fichier `.ovhconfig` à la racine de votre projet:

```
app.engine=php
app.engine.version=8.2
http.firewall=none
environment=production
container.image=stable
```

### Heroku

Le fichier `Procfile` est déjà configuré. Définissez les variables d'environnement suivantes:
- `APP_ENV`: prod
- `APP_SECRET`: votre_secret_unique
- `DATABASE_URL`: l'URL fournie par votre add-on de base de données

## Dépannage

Si vous rencontrez des erreurs 500, vérifiez les logs:
- Apache: `error.log`
- Nginx: `error.log`
- Application: `var/log/prod.log`

Pour plus d'informations, consultez la documentation officielle de Symfony sur le déploiement: https://symfony.com/doc/current/deployment.html # Updated for deployment

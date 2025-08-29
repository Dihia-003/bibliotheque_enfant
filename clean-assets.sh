#!/bin/bash

echo "🧹 Nettoyage des assets compilés..."

# Supprimer les assets compilés
rm -rf public/assets/
rm -rf var/cache/

# Recréer les dossiers vides
mkdir -p public/assets/styles
mkdir -p var/cache/prod

echo "✅ Assets nettoyés !"
echo "🔄 Redéployez maintenant avec : ./deploy-render.sh"

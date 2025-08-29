#!/bin/bash

echo "🚀 Déploiement automatisé sur Render..."

# Vérification que nous sommes sur la branche principale
if [[ $(git branch --show-current) != "main" && $(git branch --show-current) != "master" ]]; then
    echo "❌ Erreur: Vous devez être sur la branche main ou master"
    exit 1
fi

# Ajout de tous les fichiers
echo "📁 Ajout des fichiers..."
git add .

# Commit des changements
echo "💾 Commit des changements..."
git commit -m "feat: Configuration Docker optimisée pour Render - Support PostgreSQL Supabase - Scripts de déploiement automatisés"

# Push vers GitHub
echo "⬆️ Push vers GitHub..."
git push origin $(git branch --show-current)

echo "✅ Déploiement initié !"
echo "🌐 Vérifiez le statut sur: https://dashboard.render.com"
echo "⏳ Le déploiement prendra environ 5-10 minutes"
echo "🔗 Votre app sera disponible sur: https://bibliotheque-enfant.onrender.com"
echo "📝 Note: Assurez-vous que votre branche est configurée comme branche par défaut sur GitHub"

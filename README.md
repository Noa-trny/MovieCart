# 🎬 MovieCart

MovieCart est une plateforme en ligne permettant aux utilisateurs de découvrir, acheter et collectionner des films.

## 📋 Description

MovieCart offre une expérience complète pour les amateurs de cinéma. La plateforme permet de parcourir un vaste catalogue de films, de les filtrer par catégorie, réalisateur ou titre, et de les ajouter à votre collection personnelle.

## ✨ Fonctionnalités

- 🔍 Recherche avancée de films (par titre, réalisateur, acteur)
- 🔖 Navigation par catégories
- 👤 Profils réalisateurs avec filmographie complète
- 🛒 Système de panier d'achat intuitif
- 👤 Gestion de compte utilisateur
- 📚 Collection personnelle de films
- 👑 Interface d'administration
- 📱 Design responsive pour tous les appareils

## 🔧 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx)
- Composer (pour la gestion des dépendances)

## 🚀 Installation

1. **Clonez le dépôt**
   ```bash
   git clone https://github.com/noa-trny/moviecart.git
   cd moviecart
   ```

2. **Configuration du serveur web**
   - Configurez votre serveur web pour pointer vers le dossier `public/` comme racine du site
   - Assurez-vous que le module de réécriture (mod_rewrite pour Apache) est activé

3. **Base de données**
   ```bash
   mysql -u votreUtilisateur -p < sql/database.sql
   ```

4. **Configuration**
   - Copiez le fichier de configuration d'exemple
     ```bash
     cp config/config.sample.php config/config.php
     ```
   - Modifiez le fichier `config/config.php` avec vos informations de connexion à la base de données
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'votre_utilisateur');
     define('DB_PASS', 'votre_mot_de_passe');
     define('DB_NAME', 'moviecart');
     define('SITE_URL', 'http://localhost/moviecart');
     ```

5. **Accès à l'application**
   - Accédez à l'application via l'URL configurée (par défaut: http://localhost/moviecart)

## 📁 Structure du projet

```
moviecart/
├── config/         # Configuration de l'application
├── database/       # Scripts et migrations de base de données
├── public/         # Point d'entrée public de l'application
│   ├── assets/     # CSS, JS, images
│   ├── pages/      # Pages publiques
│   ├── categories/ # Pages de catégories
│   └── index.php   # Point d'entrée principal
├── sql/            # Scripts SQL pour la création de la base de données
└── src/            # Code source de l'application
    ├── api/        # API endpoints
    ├── auth/       # Gestion de l'authentification
    ├── config/     # Configuration interne
    ├── models/     # Modèles de données
    ├── utils/      # Fonctions utilitaires
    └── views/      # Templates et vues
```

## 🔑 Comptes par défaut

### Administrateur
- Email: admin@moviecart.com
- Mot de passe: admin123

### Utilisateur test
- Email: user@example.com
- Mot de passe: password123

## 🛠️ Technologies utilisées

- 🐘 PHP pour le backend
- 🗄️ MySQL pour la base de données
- 🎨 HTML5/CSS3 pour la structure et le style
- 💅 Tailwind CSS pour le framework CSS
- 📊 JavaScript pour les interactions côté client
- 🅰️ Font Awesome pour les icônes

## 🚧 Développement

Pour contribuer au projet:

1. Créez une branche pour votre fonctionnalité
   ```bash
   git checkout -b feature/ma-nouvelle-fonctionnalite
   ```

2. Commitez vos changements
   ```bash
   git commit -m 'Ajout d'une nouvelle fonctionnalité'
   ```

3. Poussez vers la branche
   ```bash
   git push origin feature/ma-nouvelle-fonctionnalite
   ```

4. Ouvrez une Pull Request

## 👥 Auteurs

- Noa Tournoy - Développeur
- Noa Panfil - Développeur

## 📜 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails. 
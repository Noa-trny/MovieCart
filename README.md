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

## 🚀 Installation

1. **Clonez le dépôt**
   ```bash
   git clone https://github.com/noa-trny/moviecart.git
   cd moviecart
   ```

2. **Configuration du serveur web**
   - Configurez votre serveur web pour pointer vers le dossier `public/pages/` comme racine du site
   - Assurez-vous que le module de réécriture (mod_rewrite pour Apache) est activé

3. **Base de données**
   ```bash
   mysql -u votreUtilisateur -p < database/schema.sql
   ```

4. **Configuration**
   - Modifiez le fichier `src/config/wamp.php` avec vos informations de connexion à la base de données
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'votre_utilisateur');
     define('DB_PASS', 'votre_mot_de_passe');
     define('DB_NAME', 'moviecart');
     define('SITE_URL', 'http://localhost/MovieCart/public/pages');
     ```

5. **Accès à l'application**
   - Accédez à l'application via l'URL configurée (par défaut: http://localhost/MovieCart/public/pages)

## 📁 Structure du projet

```
MovieCart/
├── config/               # Configuration générale de l'application
│   └── config.php        # Configuration principale
├── database/             # Scripts de base de données
│   └── schema.sql        # Schéma de base de données
├── public/               # Fichiers accessibles publiquement
│   ├── assets/           # Ressources statiques
│   │   ├── css/          # Fichiers CSS
│   │   │   ├── components/ # Composants CSS réutilisables
│   │   │   │   └── dropdown.css # Styles des menus déroulants
│   │   │   ├── common.css # Styles communs à toutes les pages
│   │   │   ├── profile.css # Styles de la page de profil
│   │   │   └── ... (autres styles spécifiques)
│   │   └── images/       # Images et icônes
│   └── pages/            # Pages de l'application
│       ├── categories/   # Pages de catégories de films
│       │   ├── action.php # Films d'action
│       │   ├── adventure.php # Films d'aventure
│       │   └── ... (autres catégories)
│       ├── index.php     # Page d'accueil
│       ├── movie.php     # Page de détail d'un film
│       ├── profile.php   # Page de profil utilisateur
│       ├── cart.php      # Page du panier
│       ├── checkout.php  # Page de paiement
│       ├── login.php     # Page de connexion
│       └── ... (autres pages)
└── src/                  # Code source de l'application
    ├── api/              # Définitions d'API (non utilisées actuellement)
    ├── config/           # Configuration interne
    │   ├── config.php    # Variables de configuration
    │   ├── database.php  # Configuration de la base de données
    │   └── wamp.php      # Configuration spécifique pour WAMP
    ├── models/           # Modèles de données
    │   └── Library.php   # Gestion de la bibliothèque de films
    ├── utils/            # Fonctions utilitaires
    │   └── functions.php # Fonctions communes réutilisables
    └── views/            # Templates et vues
        └── layouts/      # Layouts principaux
            └── main.php  # Layout principal de l'application
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
- 📊 JavaScript pour les interactions côté client
- 🅰️ Font Awesome pour les icônes

## 👥 Auteurs

- Noa Tournoy - Développeur
- Noa Panfil - Développeur

## 📜 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails. 
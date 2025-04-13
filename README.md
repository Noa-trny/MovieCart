# MovieCart

MovieCart est une plateforme en ligne permettant aux utilisateurs de découvrir, acheter et collectionner des films.

## Fonctionnalités

- Parcourir le catalogue de films
- Rechercher des films par titre ou réalisateur
- Explorer les films par catégorie
- Consulter les détails d'un film
- Découvrir tous les films d'un réalisateur
- Gérer un panier d'achats
- S'inscrire et se connecter
- Gérer son profil et sa collection de films

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx)

## Installation

1. Clonez le dépôt :
   ```
   git clone https://github.com/noa-trny/moviecart.git
   cd moviecart
   ```

2. Configurez votre serveur web pour pointer vers le dossier `public/` comme racine du site.

3. Importez la base de données :
   ```
   mysql -u votreUtilisateur -p < sql/database.sql
   ```

4. Modifiez le fichier `config/config.php` avec vos informations de connexion à la base de données :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'votre_utilisateur');
   define('DB_PASS', 'votre_mot_de_passe');
   define('DB_NAME', 'moviecart');
   ```

5. Assurez-vous que la constante `SITE_URL` dans `config/config.php` correspond à votre configuration :
   ```php
   define('SITE_URL', 'http://localhost/moviecart');
   ```

## Structure du projet

- `public/` - Point d'entrée de l'application, contient les fichiers accessibles publiquement
- `config/` - Fichiers de configuration
- `src/` - Code source de l'application
  - `auth/` - Scripts d'authentification
  - `models/` - Modèles de données
  - `utils/` - Fonctions utilitaires
  - `views/` - Templates et vues
- `sql/` - Scripts SQL pour la création de la base de données

## Compte administrateur par défaut

- Email: admin@moviecart.com
- Mot de passe: admin123

## Technologies utilisées

- PHP
- MySQL
- HTML/CSS
- Tailwind CSS
- Font Awesome

## Auteurs

- [Votre nom] - Développeur principal

## Licence

Ce projet est sous licence MIT. 
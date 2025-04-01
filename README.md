# MovieCart - Système de e-commerce pour films

## À propos
MovieCart est une plateforme web complète permettant la vente en ligne de films. Le site comprend un catalogue de films, un système d'authentification, un panier d'achat, et un système de paiement. Il est développé avec PHP, MySQL et utilise TailwindCSS pour le design.

## Fonctionnalités
- **Catalogue de films**: Parcourir les films par catégorie (Action, Drame, etc.)
- **Recherche**: Trouver des films par titre ou réalisateur
- **Page détaillée**: Vue détaillée pour chaque film avec informations, acteurs et réalisateur
- **Authentification**: Système complet d'inscription et de connexion
- **Panier d'achat**: Ajouter, supprimer et gérer les articles du panier
- **Paiement**: Processus de paiement simple
- **Profil utilisateur**: Gestion de compte et historique des achats

## Configuration requise
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx)
- WampServer (pour l'installation locale)

## Installation

### 1. Configuration de WampServer
1. Téléchargez et installez [WampServer](https://www.wampserver.com/)
2. Démarrez WampServer
3. Vérifiez que les modules Apache suivants sont activés:
   - mod_rewrite
   - mod_headers

### 2. Configuration du projet
1. Clonez ou téléchargez ce projet dans le répertoire `www` de WampServer:
   ```
   C:\wamp64\www\MovieCart\
   ```

2. Assurez-vous que la structure de dossiers suivante existe:
   ```
   C:\wamp64\www\MovieCart\
   ├── public\
   │   ├── index.php
   │   ├── login.php
   │   ├── register.php
   │   ├── logout.php
   │   ├── movie.php
   │   ├── search.php
   │   ├── cart.php
   │   ├── profile.php
   │   ├── categories\
   │   │   ├── action.php
   │   │   └── drama.php
   ├── src\
   │   ├── config\
   │   │   ├── config.php
   │   │   ├── database.php
   │   │   └── wamp.php
   │   └── views\
   │       └── layouts\
   │           └── main.php
   ├── database\
   │   └── schema.sql
   └── uploads\
       └── posters\
   ```

3. Créez ces dossiers s'ils n'existent pas:
   ```
   mkdir C:\wamp64\www\MovieCart\src\config
   mkdir C:\wamp64\www\MovieCart\src\views\layouts
   mkdir C:\wamp64\www\MovieCart\database
   mkdir C:\wamp64\www\MovieCart\uploads\posters
   ```

### 3. Configuration de la base de données
1. Ouvrez phpMyAdmin (http://localhost/phpmyadmin)
2. Créez une nouvelle base de données nommée `moviecart`
3. Importez le fichier `database/schema.sql` dans cette base de données

### 4. Configuration du site
Le fichier `src/config/wamp.php` contient les paramètres de configuration:
- Connexion à la base de données
- URL du site
- Chemins des dossiers d'upload
- Configuration des erreurs

Par défaut, le site est configuré pour utiliser:
- Base de données: `moviecart`
- Utilisateur: `root`
- Mot de passe: `` (vide)
- URL du site: `http://localhost/MovieCart/public`

### 5. Permissions
Assurez-vous que le dossier `uploads/posters` a les permissions d'écriture:
- Clic droit sur le dossier → Propriétés → Sécurité
- Ajoutez l'utilisateur `IUSR` et `IIS_IUSRS` avec les droits de lecture et d'écriture

### 6. Accès au site
1. Ouvrez votre navigateur
2. Accédez à http://localhost/MovieCart/public

## Utilisation
### Compte utilisateur
Un compte administrateur par défaut est disponible:
- Email: `admin@example.com`
- Mot de passe: `password123`

### Navigation sur le site
- **Page d'accueil**: Affiche les films récents et les catégories
- **Catégories**: Parcourir les films par genre
- **Recherche**: Trouver des films par titre ou réalisateur
- **Détail du film**: Cliquez sur un film pour voir ses détails complets
- **Panier**: Ajoutez des films à votre panier et passez à la caisse
- **Profil**: Gérez votre compte et consultez vos achats

## Développement

### Structure du code
- **public**: Fichiers accessibles par le navigateur
- **src/config**: Fichiers de configuration
- **src/views**: Templates et layouts
- **database**: Schéma de la base de données
- **uploads**: Fichiers téléchargés (affiches de films)

### Base de données
Le schéma de la base de données est organisé comme suit:
- `users`: Utilisateurs du site
- `categories`: Catégories de films
- `directors`: Réalisateurs
- `actors`: Acteurs
- `movies`: Films
- `movie_actors`: Relation entre films et acteurs
- `orders`: Commandes
- `order_items`: Articles des commandes

## Fonctionnalités techniques

### Authentification
- Hachage sécurisé des mots de passe avec `password_hash`
- Protection CSRF sur tous les formulaires
- Validation des entrées utilisateur

### Sécurité
- Préparation des requêtes SQL pour prévenir les injections
- Échappement des sorties HTML pour prévenir le XSS
- Validation et nettoyage des entrées utilisateur

### Responsive Design
Le site utilise TailwindCSS pour un design adaptatif qui fonctionne sur:
- Ordinateurs de bureau
- Tablettes
- Smartphones

## Dépannage

### Problèmes courants
1. **Erreur "Failed to open stream"**
   - Vérifiez que tous les dossiers existent dans la structure attendue
   
2. **Erreur "Call to undefined function"**
   - Vérifiez que toutes les extensions PHP nécessaires sont activées dans WampServer

3. **Erreur de base de données**
   - Vérifiez que la base de données `moviecart` existe
   - Vérifiez que le schéma a été importé correctement
   - Vérifiez les identifiants de connexion dans `src/config/wamp.php`

4. **Images qui ne s'affichent pas**
   - Vérifiez les permissions du dossier `uploads/posters`
   - Vérifiez que les chemins sont correctement définis dans `src/config/wamp.php`

### Logs
En cas d'erreur, consultez les logs Apache:
- `C:\wamp64\logs\apache_error.log`

## Crédits
Ce projet a été développé en tant que site e-commerce de démonstration pour Internet Movies DataBase & co. 
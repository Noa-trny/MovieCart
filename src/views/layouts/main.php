<?php
require_once __DIR__ . '/../../utils/functions.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>MovieCart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="<?= ASSETS_URL ?>/images/icon.png" type="image/png">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/common.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/categories.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/components/dropdown.css">
    <?php
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    $currentDir = basename(dirname($_SERVER['PHP_SELF']));
    
    if ($currentPage === 'index') {
        echo '<link rel="stylesheet" href="' . ASSETS_URL . '/css/index.css">';
    } elseif (defined('PUBLIC_PATH') && file_exists(PUBLIC_PATH . '/assets/css/' . $currentPage . '.css')) {
        echo '<link rel="stylesheet" href="' . ASSETS_URL . '/css/' . $currentPage . '.css">';
    }
    
    if (isset($customCss)) {
        $cssPath = ASSETS_URL . $customCss;
        echo '<link rel="stylesheet" href="' . $cssPath . '">';
    }
    
    if (isset($additionalCss)) {
        $additionalCssPath = ASSETS_URL . $additionalCss;
        echo '<link rel="stylesheet" href="' . $additionalCssPath . '">';
    }
    
    if (isset($customHead)) {
        echo $customHead;
    }
    ?>
    <meta name="description" content="Plateforme de vente de films en ligne. Trouvez, achetez et téléchargez vos films préférés.">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="<?= SITE_URL ?>/index.php" class="logo">
                    <i class="fas fa-film"></i> MovieCart
                </a>
                
                <div class="search-form">
                    <form action="<?= SITE_URL ?>/search.php" method="GET">
                        <div class="search-input-group">
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="Rechercher un film..." 
                                value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                            >
                            <button type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="nav-links">
                    <a href="<?= SITE_URL ?>/index.php" class="nav-link">Accueil</a>
                    
                    <div class="dropdown">
                        <button onclick="toggleDropdown(this.parentNode)" class="dropdown-toggle nav-link">
                            Catégories <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="<?= SITE_URL ?>/categories/action.php" class="dropdown-item">Action</a>
                            <a href="<?= SITE_URL ?>/categories/drama.php" class="dropdown-item">Drame</a>
                            <a href="<?= SITE_URL ?>/categories/adventure.php" class="dropdown-item">Aventure</a>
                            <a href="<?= SITE_URL ?>/categories/scifi.php" class="dropdown-item">Science-fiction</a>
                            <a href="<?= SITE_URL ?>/categories/comedy.php" class="dropdown-item">Comédie</a>
                        </div>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= SITE_URL ?>/cart.php" class="nav-link cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if (isset($_SESSION[CART_SESSION_KEY]) && count($_SESSION[CART_SESSION_KEY]) > 0): ?>
                                <span class="cart-badge">
                                    <?= count($_SESSION[CART_SESSION_KEY]) ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown">
                            <button onclick="toggleDropdown(this.parentNode)" class="dropdown-toggle nav-link">
                                <i class="fas fa-user"></i> Compte <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="<?= SITE_URL ?>/profile.php" class="dropdown-item">
                                    <i class="fas fa-user-circle"></i> Profil
                                </a>
                                <a href="<?= SITE_URL ?>/logout.php" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/login.php" class="nav-link">Connexion</a>
                        <a href="<?= SITE_URL ?>/register.php" class="btn btn-primary">
                            Inscription
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    
    <div class="mobile-search">
        <div class="container">
            <form action="<?= SITE_URL ?>/search.php" method="GET">
                <div class="search-input-group">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Rechercher un film..." 
                        value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                    >
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="container">
            <div class="flash-message <?= $_SESSION['flash_type'] === 'success' ? 'flash-success' : 'flash-error' ?>">
                <?= $_SESSION['flash_message'] ?>
            </div>
        </div>
        <?php 
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>
    
    <main>
        <div class="container">
            <?= $content ?>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-container">
                <p>© <?= date('Y') ?> <?= SITE_NAME ?>. Tous droits réservés.</p>
                <div class="footer-links">
                    <a href="<?= SITE_URL ?>/index.php" class="footer-link">Accueil</a>
                    <a href="<?= SITE_URL ?>/categories/action.php" class="footer-link">Action</a>
                    <a href="<?= SITE_URL ?>/categories/drama.php" class="footer-link">Drame</a>
                    <a href="<?= SITE_URL ?>/categories/adventure.php" class="footer-link">Aventure</a>
                    <a href="<?= SITE_URL ?>/categories/scifi.php" class="footer-link">Science-fiction</a>
                    <a href="<?= SITE_URL ?>/categories/comedy.php" class="footer-link">Comédie</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
    function toggleDropdown(dropdown) {
        dropdown.classList.toggle('active');
        
        document.addEventListener('click', function closeDropdowns(e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
                document.removeEventListener('click', closeDropdowns);
            }
        });
    }
    </script>
</body>
</html> 
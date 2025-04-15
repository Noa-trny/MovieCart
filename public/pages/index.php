<?php
$pageTitle = 'Accueil';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

$featuredMovies = getFeaturedMovies();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();
?>

<section class="hero">
    <div class="container hero-container">
        <div class="hero-content">
            <h1 class="hero-title">Bienvenue sur MovieCart</h1>
            <p class="hero-description">Découvrez, achetez et collectionnez vos films préférés.</p>
            <div class="hero-buttons">
                <a href="<?= SITE_URL ?>/../categories/action.php" class="btn btn-primary">
                    Films d'action
                </a>
                <a href="<?= SITE_URL ?>/../categories/drama.php" class="btn-outline">
                    Films dramatiques
                </a>
            </div>
        </div>
        <div class="hero-image-container">
            <img src="<?= ASSETS_URL ?>/images/hero-image.jpg" alt="Collection de films" class="hero-image">
        </div>
    </div>
</section>

<section class="search-section">
    <div class="container">
        <div class="search-form-container">
            <h2 class="search-title">Recherchez votre prochain film</h2>
            <form action="<?= SITE_URL ?>/search.php" method="GET">
                <div class="search-input-group">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Rechercher par titre ou réalisateur..." 
                    >
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Films en vedette</h2>
    
    <?php if (empty($featuredMovies)): ?>
        <div class="empty-state">
            <i class="fas fa-film empty-icon"></i>
            <h3 class="movie-title">Aucun film en vedette</h3>
            <p>Nos films en vedette seront bientôt disponibles.</p>
        </div>
    <?php else: ?>
        <div class="movie-grid">
            <?php foreach ($featuredMovies as $movie): ?>
                <div class="movie-card">
                    <div class="movie-poster-container">
                        <img 
                            src="<?= isset($movie['poster_path']) ? $movie['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" 
                            alt="<?= htmlspecialchars($movie['title']) ?>" 
                            class="movie-poster"
                        >
                        <?php if (isset($movie['category_name'])): ?>
                            <div class="movie-category">
                                <?= htmlspecialchars($movie['category_name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
                        <?php if (isset($movie['director_id']) && isset($movie['director_name'])): ?>
                            <p class="movie-director">
                                Réalisé par 
                                <a href="<?= SITE_URL ?>/../director.php?id=<?= $movie['director_id'] ?>">
                                    <?= htmlspecialchars($movie['director_name']) ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <div class="movie-footer">
                            <span class="movie-price"><?= number_format($movie['price'], 2) ?> €</span>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?= SITE_URL ?>/../add-to-cart.php?id=<?= $movie['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Ajouter
                                </a>
                            <?php else: ?>
                                <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary">
                                    <i class="fas fa-lock"></i> Connexion
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <a href="<?= SITE_URL ?>/categories.php" class="btn btn-primary btn-center">
        Voir tous les films
    </a>
</section>

<section class="container">
    <h2 class="section-title">Explorez par catégorie</h2>
    
    <div class="category-grid">
        <a href="<?= SITE_URL ?>/categories/action.php" class="category-card">
            <div class="category-icon category-action">
                <i class="fas fa-fire"></i>
            </div>
            <div class="category-name">Action</div>
        </a>
        
        <a href="<?= SITE_URL ?>/categories/drama.php" class="category-card">
            <div class="category-icon category-drama">
                <i class="fas fa-theater-masks"></i>
            </div>
            <div class="category-name">Drame</div>
        </a>
    </div>
</section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?>

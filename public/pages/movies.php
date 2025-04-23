<?php
$pageTitle = 'Tous les films';
$customCss = '/css/index.css';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

$movies = getAllMovies();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();
?>

<section class="container">
    <div class="section-header">
        <h1 class="section-title">Tous les films</h1>
        <p class="section-description">Découvrez notre collection complète de films disponibles à l'achat.</p>
    </div>
    
    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <i class="fas fa-film empty-icon"></i>
            <h3 class="movie-title">Aucun film disponible</h3>
            <p>Notre catalogue de films sera bientôt disponible.</p>
        </div>
    <?php else: ?>
        <div class="movie-grid">
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <div class="movie-poster-container">
                        <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                            <img 
                                src="<?= isset($movie['poster_path']) ? $movie['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" 
                                alt="<?= htmlspecialchars($movie['title']) ?>" 
                                class="movie-poster"
                            >
                        </a>
                        <?php if (isset($movie['category_name'])): ?>
                            <div class="movie-category">
                                <?= htmlspecialchars($movie['category_name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title">
                            <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($movie['title']) ?>
                            </a>
                        </h3>
                        <?php if (isset($movie['director_id']) && isset($movie['director_name'])): ?>
                            <p class="movie-director">
                                Réalisé par 
                                <a href="<?= SITE_URL ?>/director.php?id=<?= $movie['director_id'] ?>">
                                    <?= htmlspecialchars($movie['director_name']) ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <div class="movie-footer">
                            <span class="movie-price"><?= number_format($movie['price'], 2) ?> €</span>
                            <?php if (isLoggedIn()): ?>
                                <?php if (hasUserPurchasedMovie($_SESSION['user_id'], $movie['id'])): ?>
                                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" class="btn btn-secondary">
                                        <i class="fas fa-check"></i> Déjà acheté
                                    </a>
                                <?php else: ?>
                                    <a href="<?= SITE_URL ?>/add-to-cart.php?id=<?= $movie['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart"></i> Ajouter
                                    </a>
                                <?php endif; ?>
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
</section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?>
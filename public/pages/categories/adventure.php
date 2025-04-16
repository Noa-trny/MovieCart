<?php
$pageTitle = 'Films d\'aventure';
$category = 'Adventure';

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$movies = getMoviesByCategory($category);

ob_start();
?>

<div class="category-header">
    <h1 class="category-title">Films d'aventure</h1>
    <p class="category-description">Partez à l'aventure avec notre sélection de films palpitants et d'explorations extraordinaires.</p>
</div>

<div class="movies-grid">
    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <i class="fas fa-film empty-icon"></i>
            <h2 class="movie-title">Aucun film disponible</h2>
            <p>Nous travaillons à étoffer notre catalogue de films d'aventure.</p>
        </div>
    <?php else: ?>
        <?php foreach ($movies as $movie): ?>
            <div class="movie-card">
                <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                    <img src="<?= isset($movie['poster_path']) ? $movie['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                </a>
                <div class="movie-info">
                    <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
                    <p class="movie-director">Réalisé par <?= htmlspecialchars($movie['director_name']) ?></p>
                    <div class="movie-actions">
                        <span class="movie-price"><?= number_format($movie['price'], 2) ?> €</span>
                        <?php if (isLoggedIn()): ?>
                            <a href="<?= SITE_URL ?>/add-to-cart.php?id=<?= $movie['id'] ?>" class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i> Ajouter
                            </a>
                        <?php else: ?>
                            <a href="<?= SITE_URL ?>/login.php" class="login-prompt">
                                <i class="fas fa-lock"></i> Connectez-vous
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../src/views/layouts/main.php';
?> 
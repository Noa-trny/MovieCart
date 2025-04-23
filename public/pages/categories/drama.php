<?php
$pageTitle = 'Films dramatiques';
$category = 'Drama';

$rootPath = __DIR__ . '/../../../';
require_once $rootPath . 'config/config.php';
require_once $rootPath . 'src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$movies = getMoviesByCategory($category, 20);

ob_start();
?>

<div class="container">
    <div class="category-header">
        <h1>Films dramatiques</h1>
        <p>Découvrez notre sélection de films dramatiques et d'émotions intenses</p>
    </div>
    
    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <i class="fas fa-film"></i>
            <h2>Aucun film disponible</h2>
            <p>Nous travaillons à étoffer notre catalogue de films dramatiques.</p>
        </div>
    <?php else: ?>
        <div class="movies-grid">
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                        <img src="<?= $movie['poster_path'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
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
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once $rootPath . 'src/views/layouts/main.php';
?> 
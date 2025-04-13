<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($movieId <= 0) {
    setFlashMessage('Film non trouvé.', 'error');
    redirectTo(SITE_URL . '/index.php');
}

$movie = getMovieById($movieId);

if (!$movie) {
    setFlashMessage('Film non trouvé.', 'error');
    redirectTo(SITE_URL . '/index.php');
}

$pageTitle = $movie['title'];

ob_start();
?>

<div class="movie-container">
    <div class="movie-detail-card">
        <div class="movie-details">
            <div class="movie-poster-section">
                <?php if (isset($movie['poster_path']) && !empty($movie['poster_path'])): ?>
                    <img 
                        src="<?= $movie['poster_path'] ?>" 
                        alt="<?= htmlspecialchars($movie['title']) ?>" 
                        class="movie-poster-img"
                    >
                <?php else: ?>
                    <div class="empty-poster">
                        <i class="fas fa-film"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="movie-info-section">
                <h1 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h1>
                
                <div class="movie-meta">
                    <?php if (isset($movie['release_year'])): ?>
                        <span class="movie-meta-item"><?= $movie['release_year'] ?></span>
                    <?php endif; ?>
                    
                    <?php if (isset($movie['duration'])): ?>
                        <span class="movie-meta-item">
                            <i class="fas fa-clock"></i> <?= $movie['duration'] ?> min
                        </span>
                    <?php endif; ?>
                    
                    <?php if (isset($movie['category_name'])): ?>
                        <span class="movie-meta-item category">
                            <?= htmlspecialchars($movie['category_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($movie['director_id']) && isset($movie['director_name'])): ?>
                    <div class="movie-director">
                        <h2 class="movie-director-title">Réalisateur:</h2>
                        <a href="<?= SITE_URL ?>/director.php?id=<?= $movie['director_id'] ?>" class="director-link">
                            <?= htmlspecialchars($movie['director_name']) ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="movie-description">
                    <h2 class="movie-description-title">Synopsis:</h2>
                    <p class="movie-description-content">
                        <?= isset($movie['description']) && !empty($movie['description']) ? nl2br(htmlspecialchars($movie['description'])) : 'Aucune description disponible pour ce film.' ?>
                    </p>
                </div>
                
                <div class="movie-action-bar">
                    <span class="movie-price"><?= number_format($movie['price'], 2) ?> €</span>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= SITE_URL ?>/add-to-cart.php?id=<?= $movie['id'] ?>" class="btn btn-primary btn-add-to-cart">
                            <i class="fas fa-cart-plus"></i> Ajouter au panier
                        </a>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/login.php?redirect=<?= urlencode(SITE_URL . '/movie.php?id=' . $movie['id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-lock"></i> Connexion pour acheter
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
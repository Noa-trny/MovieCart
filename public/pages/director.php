<?php
$pageTitle = 'Films par réalisateur';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$directorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn = connectDB();
$stmt = $conn->prepare("SELECT id, name, biography FROM directors WHERE id = ?");
$stmt->bind_param("i", $directorId);
$stmt->execute();
$result = $stmt->get_result();
$director = $result->fetch_assoc();
$stmt->close();

if (!$director) {
    setFlashMessage('Réalisateur non trouvé.', 'error');
    redirectTo(SITE_URL);
}

$movies = getMoviesByDirector($directorId);

$additionalStyles = '<link rel="stylesheet" href="' . ASSETS_URL . '/css/director.css">';

ob_start();
?>

<div class="director-container">
    <a href="javascript:history.back()" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
    
    <div class="director-card">
        <h1 class="director-name"><?= htmlspecialchars($director['name']) ?></h1>
        <?php if (isset($director['biography']) && !empty($director['biography'])): ?>
            <p class="director-bio"><?= nl2br(htmlspecialchars($director['biography'])) ?></p>
        <?php else: ?>
            <p class="no-bio">Aucune biographie disponible pour ce réalisateur.</p>
        <?php endif; ?>
    </div>
    
    <h2 class="section-title">Films de <?= htmlspecialchars($director['name']) ?></h2>
    
    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <i class="fas fa-film empty-icon"></i>
            <h3 class="empty-title">Aucun film disponible</h3>
            <p class="empty-description">Nous n'avons pas encore de films de ce réalisateur dans notre catalogue.</p>
        </div>
    <?php else: ?>
        <div class="movies-grid">
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                        <img src="<?= isset($movie['poster_path']) ? $movie['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" 
                             alt="<?= htmlspecialchars($movie['title']) ?>" 
                             class="movie-poster">
                    </a>
                    <div class="movie-info">
                        <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" class="block">
                            <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
                        </a>
                        <div class="movie-footer">
                            <span class="movie-price"><?= number_format($movie['price'], 2) ?> €</span>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?= SITE_URL ?>/add-to-cart.php?id=<?= $movie['id'] ?>" class="btn-add">
                                    <i class="fas fa-shopping-cart"></i> Ajouter
                                </a>
                            <?php else: ?>
                                <a href="<?= SITE_URL ?>/login.php" class="btn-login">
                                    <i class="fas fa-lock"></i> Connexion
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
$conn->close();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
<?php
$pageTitle = 'Recherche';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($searchQuery)) {
    $results = searchMovies($searchQuery);
}

ob_start();
?>

<div class="search-container">
    <h1 class="search-title">Rechercher un film</h1>
    
    <div class="search-form-large">
        <form action="<?= SITE_URL ?>/search.php" method="GET">
            <div class="search-input-group-large">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Titre, réalisateur..." 
                    value="<?= htmlspecialchars($searchQuery) ?>"
                    required
                >
                <button type="submit">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </div>
        </form>
    </div>
    
    <?php if (!empty($searchQuery)): ?>
        <?php if (empty($results)): ?>
            <div class="empty-results">
                <i class="fas fa-search empty-icon"></i>
                <h2 class="empty-title">Aucun résultat trouvé</h2>
                <p class="empty-description">Aucun film ne correspond à votre recherche "<?= htmlspecialchars($searchQuery) ?>"</p>
                
                <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">
                    Retour à l'accueil
                </a>
            </div>
        <?php else: ?>
            <div class="results-header">
                <h2 class="results-title">Résultats de recherche</h2>
                <span class="results-count"><?= count($results) ?> films trouvés</span>
            </div>
            
            <div class="search-results">
                <?php foreach ($results as $movie): ?>
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
                            
                            <?php if (isset($movie['director_name'])): ?>
                                <p class="movie-director">
                                    Réalisé par <?= htmlspecialchars($movie['director_name']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="movie-footer">
                                <span class="movie-price"><?= number_format($movie['price'], 2) ?> €</span>
                                <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" class="btn-details">
                                    Détails
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="categories-section">
            <h2 class="categories-title">Parcourir par catégorie</h2>
            
            <div class="categories-grid">
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
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
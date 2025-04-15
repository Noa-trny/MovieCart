<?php
$pageTitle = 'Recherche';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$query = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';
$movies = [];

if (!empty($query)) {
    $movies = searchMovies($query);
}

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Résultats de recherche pour "<?= htmlspecialchars($query) ?>"</h1>
    
    <div class="mb-8">
        <form action="<?= SITE_URL ?>/search.php" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <input 
                    type="text" 
                    name="q" 
                    value="<?= htmlspecialchars($query) ?>"
                    placeholder="Rechercher par titre ou réalisateur..." 
                    class="w-full px-6 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i> Rechercher
            </button>
        </form>
    </div>
    
    <?php if (empty($query)): ?>
        <div class="bg-gray-100 p-8 rounded-lg text-center">
            <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Commencez votre recherche</h2>
            <p class="text-gray-600">Entrez un titre de film ou un nom de réalisateur pour trouver des films.</p>
        </div>
    <?php elseif (empty($movies)): ?>
        <div class="bg-gray-100 p-8 rounded-lg text-center">
            <i class="fas fa-film text-5xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Aucun résultat trouvé</h2>
            <p class="text-gray-600">Nous n'avons trouvé aucun film correspondant à "<?= htmlspecialchars($query) ?>".</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php foreach ($movies as $movie): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative">
                        <img src="<?= $movie['poster_path'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="w-full h-64 object-cover">
                        <div class="absolute top-2 right-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                            <?= htmlspecialchars($movie['category']) ?>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($movie['title']) ?></h3>
                        <p class="text-gray-600 mb-2">Réalisé par <a href="<?= SITE_URL ?>/director.php?id=<?= $movie['director_id'] ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($movie['director_name']) ?></a></p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xl font-bold text-blue-600"><?= number_format($movie['price'], 2) ?> €</span>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?= SITE_URL ?>/add-to-cart.php?id=<?= $movie['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i> Ajouter
                                </a>
                            <?php else: ?>
                                <a href="<?= SITE_URL ?>/login.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition-colors">
                                    <i class="fas fa-lock mr-1"></i> Connectez-vous
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
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 
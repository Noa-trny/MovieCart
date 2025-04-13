<?php
$pageTitle = 'Films dramatiques';
$category = 'Drama';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$movies = getMoviesByCategory($category);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="bg-blue-600 text-white rounded-lg p-8 flex items-center">
            <div class="mr-6 text-5xl">
                <i class="fas fa-theater-masks"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Films dramatiques</h1>
                <p class="text-xl opacity-90">Explorez notre collection de films dramatiques captivants et émouvants.</p>
            </div>
        </div>
    </div>
    
    <?php if (empty($movies)): ?>
        <div class="bg-gray-100 p-8 rounded-lg text-center">
            <i class="fas fa-film text-5xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Aucun film disponible</h2>
            <p class="text-gray-600">Nous travaillons à étoffer notre catalogue de films dramatiques.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php foreach ($movies as $movie): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                        <img src="<?= $movie['image_url'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="w-full h-64 object-cover">
                    </a>
                    <div class="p-4">
                        <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                            <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($movie['title']) ?></h3>
                        </a>
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
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
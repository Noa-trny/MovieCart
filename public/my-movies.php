<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

requireLogin();

$pageTitle = 'Ma Collection de Films';

$userId = $_SESSION['user_id'];

$movies = fetchAll(
    "SELECT DISTINCT m.*, d.name as director_name, c.name as category_name 
     FROM movies m
     JOIN order_items oi ON m.id = oi.movie_id
     JOIN orders o ON oi.order_id = o.id
     LEFT JOIN directors d ON m.director_id = d.id
     LEFT JOIN categories c ON m.category_id = c.id
     WHERE o.user_id = ? AND o.status = 'completed'
     ORDER BY m.title ASC",
    [$userId]
);

ob_start();
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2">Ma Collection de Films</h1>
    <p class="text-gray-600">Tous les films que vous avez achetés</p>
</div>

<?php if (empty($movies)): ?>
    <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <i class="fas fa-film text-gray-300 text-5xl mb-4"></i>
        <h2 class="text-2xl font-semibold mb-2">Aucun film dans votre collection</h2>
        <p class="text-gray-600 mb-6">Vous n'avez pas encore acheté de films.</p>
        <a href="<?= SITE_URL ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold">
            Parcourir les Films
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($movies as $movie): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                    <img src="<?= SITE_URL ?>/uploads/posters/<?= $movie['poster_path'] ?? 'default.jpg' ?>" 
                         alt="<?= sanitizeOutput($movie['title']) ?>"
                         class="w-full h-64 object-cover">
                </a>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2">
                        <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" class="hover:text-blue-600">
                            <?= sanitizeOutput($movie['title']) ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-2">
                        Réalisé par <?= sanitizeOutput($movie['director_name']) ?>
                    </p>
                    <p class="text-gray-600 mb-2">
                        <?= $movie['release_year'] ?> • <?= $movie['duration'] ?> min
                    </p>
                    <p class="text-gray-600">
                        Catégorie: <?= sanitizeOutput($movie['category_name']) ?>
                    </p>
                    
                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" 
                       class="mt-4 block w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center">
                        <i class="fas fa-info-circle mr-2"></i> Détails
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 
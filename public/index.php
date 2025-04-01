<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Fetch latest movies
$latestMovies = fetchAll("
    SELECT m.*, d.name as director_name, c.name as category_name 
    FROM movies m 
    LEFT JOIN directors d ON m.director_id = d.id 
    LEFT JOIN categories c ON m.category_id = c.id 
    ORDER BY m.created_at DESC 
    LIMIT 6
");

// Fetch featured movies by category
$actionMovies = fetchAll("
    SELECT m.*, d.name as director_name, c.name as category_name 
    FROM movies m 
    LEFT JOIN directors d ON m.director_id = d.id 
    LEFT JOIN categories c ON m.category_id = c.id 
    WHERE c.slug = 'action' 
    ORDER BY m.created_at DESC 
    LIMIT 4
");

$dramaMovies = fetchAll("
    SELECT m.*, d.name as director_name, c.name as category_name 
    FROM movies m 
    LEFT JOIN directors d ON m.director_id = d.id 
    LEFT JOIN categories c ON m.category_id = c.id 
    WHERE c.slug = 'drama' 
    ORDER BY m.created_at DESC 
    LIMIT 4
");

$pageTitle = 'Home';
ob_start();
?>

<!-- Hero Section -->
<div class="bg-gray-900 text-white py-16 mb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Welcome to <?= SITE_NAME ?></h1>
            <p class="text-xl text-gray-300 mb-8">Your ultimate destination for movies and entertainment</p>
            <a href="#latest-movies" class="bg-blue-500 text-white px-8 py-3 rounded-lg hover:bg-blue-600 transition duration-300">
                Explore Movies
            </a>
        </div>
    </div>
</div>

<!-- Latest Movies Section -->
<section id="latest-movies" class="mb-12">
    <h2 class="text-2xl font-bold mb-6">Latest Movies</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($latestMovies as $movie): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="<?= SITE_URL ?>/uploads/posters/<?= $movie['poster_path'] ?? 'default.jpg' ?>" 
                     alt="<?= sanitizeOutput($movie['title']) ?>"
                     class="w-full h-64 object-cover">
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2"><?= sanitizeOutput($movie['title']) ?></h3>
                    <p class="text-gray-600 mb-2">Directed by <?= sanitizeOutput($movie['director_name']) ?></p>
                    <p class="text-gray-600 mb-4"><?= sanitizeOutput($movie['category_name']) ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold">$<?= number_format($movie['price'], 2) ?></span>
                        <?php if (isLoggedIn()): ?>
                            <form action="<?= SITE_URL ?>/cart.php" method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Add to Cart
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="<?= SITE_URL ?>/login.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Login to Purchase
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Categories Section -->
<section class="mb-12">
    <h2 class="text-2xl font-bold mb-6">Browse by Category</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Action Movies -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Action Movies</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($actionMovies as $movie): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?= SITE_URL ?>/uploads/posters/<?= $movie['poster_path'] ?? 'default.jpg' ?>" 
                             alt="<?= sanitizeOutput($movie['title']) ?>"
                             class="w-full h-48 object-cover">
                        <div class="p-3">
                            <h4 class="font-semibold"><?= sanitizeOutput($movie['title']) ?></h4>
                            <p class="text-gray-600 text-sm">$<?= number_format($movie['price'], 2) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= SITE_URL ?>/categories/action.php" class="text-blue-500 hover:text-blue-600">
                    View All Action Movies →
                </a>
            </div>
        </div>

        <!-- Drama Movies -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Drama Movies</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($dramaMovies as $movie): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?= SITE_URL ?>/uploads/posters/<?= $movie['poster_path'] ?? 'default.jpg' ?>" 
                             alt="<?= sanitizeOutput($movie['title']) ?>"
                             class="w-full h-48 object-cover">
                        <div class="p-3">
                            <h4 class="font-semibold"><?= sanitizeOutput($movie['title']) ?></h4>
                            <p class="text-gray-600 text-sm">$<?= number_format($movie['price'], 2) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= SITE_URL ?>/categories/drama.php" class="text-blue-500 hover:text-blue-600">
                    View All Drama Movies →
                </a>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?>

<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/database.php';

// Set the category slug
$categorySlug = 'action';

// Fetch category information
$category = fetchOne(
    "SELECT * FROM categories WHERE slug = ?", 
    [$categorySlug]
);

// If category doesn't exist, redirect to homepage
if (!$category) {
    redirect('/');
}

// Fetch movies in this category
$movies = fetchAll(
    "SELECT m.*, d.name as director_name 
     FROM movies m 
     LEFT JOIN directors d ON m.director_id = d.id 
     WHERE m.category_id = ? 
     ORDER BY m.created_at DESC",
    [$category['id']]
);

$pageTitle = $category['name'] . ' Movies';
ob_start();
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2"><?= sanitizeOutput($category['name']) ?> Movies</h1>
    <p class="text-gray-600"><?= sanitizeOutput($category['description']) ?></p>
</div>

<?php if (empty($movies)): ?>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
        <p>No movies found in this category.</p>
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
                        Directed by 
                        <?= sanitizeOutput($movie['director_name']) ?>
                    </p>
                    <p class="text-gray-600 mb-2">
                        <?= $movie['release_year'] ?> • <?= $movie['duration'] ?> min
                    </p>
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
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
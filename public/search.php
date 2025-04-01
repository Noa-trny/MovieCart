<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Get search query
$query = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
$pageTitle = 'Search Results';

// Initialize movies array
$movies = [];

// Perform search if query exists
if ($query) {
    // Search for movies by title or director name
    $searchParam = '%' . $query . '%';
    $movies = fetchAll(
        "SELECT m.*, d.name as director_name, c.name as category_name
         FROM movies m
         LEFT JOIN directors d ON m.director_id = d.id
         LEFT JOIN categories c ON m.category_id = c.id
         WHERE m.title LIKE ? OR d.name LIKE ?
         ORDER BY 
            CASE 
                WHEN m.title LIKE ? THEN 1
                WHEN d.name LIKE ? THEN 2
                ELSE 3
            END,
            m.created_at DESC",
        [$searchParam, $searchParam, $searchParam, $searchParam]
    );
}

ob_start();
?>

<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4">Search Results</h1>
        <p class="text-gray-600">
            <?php if ($query): ?>
                Showing results for: <span class="font-semibold"><?= sanitizeOutput($query) ?></span>
            <?php else: ?>
                Enter a search term to find movies
            <?php endif; ?>
        </p>
    </div>

    <?php if (empty($movies)): ?>
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
            <h2 class="text-2xl font-semibold mb-2">No results found</h2>
            <p class="text-gray-600 mb-6">We couldn't find any movies matching your search term.</p>
            <a href="<?= SITE_URL ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold">
                Browse All Movies
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
                            Directed by 
                            <?= sanitizeOutput($movie['director_name']) ?>
                        </p>
                        <p class="text-gray-600 mb-2">
                            Category: <?= sanitizeOutput($movie['category_name']) ?>
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
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 
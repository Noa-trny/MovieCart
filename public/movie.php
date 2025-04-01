<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Get movie ID from URL
$movieId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// If no valid ID provided, redirect to homepage
if (!$movieId) {
    redirect('/');
}

// Fetch movie details with director and category info
$movie = fetchOne(
    "SELECT m.*, d.name as director_name, d.biography as director_biography,
            c.name as category_name, c.slug as category_slug
     FROM movies m
     LEFT JOIN directors d ON m.director_id = d.id
     LEFT JOIN categories c ON m.category_id = c.id
     WHERE m.id = ?",
    [$movieId]
);

// If movie not found, redirect to homepage
if (!$movie) {
    redirect('/');
}

// Fetch actors for this movie
$actors = fetchAll(
    "SELECT a.*, ma.role
     FROM actors a
     JOIN movie_actors ma ON a.id = ma.actor_id
     WHERE ma.movie_id = ?
     ORDER BY a.name",
    [$movieId]
);

$pageTitle = $movie['title'];
ob_start();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Movie Poster -->
    <div class="md:col-span-1">
        <img src="<?= SITE_URL ?>/uploads/posters/<?= $movie['poster_path'] ?? 'default.jpg' ?>" 
             alt="<?= sanitizeOutput($movie['title']) ?>" 
             class="w-full rounded-lg shadow-lg">
             
        <div class="mt-6 bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold border-b pb-2 mb-3">Movie Details</h3>
            <ul class="space-y-2">
                <li><strong>Category:</strong> 
                    <a href="<?= SITE_URL ?>/categories/<?= $movie['category_slug'] ?>.php" class="text-blue-600 hover:underline">
                        <?= sanitizeOutput($movie['category_name']) ?>
                    </a>
                </li>
                <li><strong>Release Year:</strong> <?= $movie['release_year'] ?></li>
                <li><strong>Duration:</strong> <?= $movie['duration'] ?> minutes</li>
                <li><strong>Price:</strong> <span class="text-lg font-bold">$<?= number_format($movie['price'], 2) ?></span></li>
            </ul>
            
            <?php if (isLoggedIn()): ?>
                <form action="<?= SITE_URL ?>/cart.php" method="POST" class="mt-4">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center">
                        <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <div class="mt-4 bg-gray-100 p-3 rounded-lg text-center">
                    <p class="mb-2">Please login to purchase this movie</p>
                    <a href="<?= SITE_URL ?>/login.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold">
                        Login to Purchase
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Movie Information -->
    <div class="md:col-span-2">
        <h1 class="text-3xl font-bold mb-2"><?= sanitizeOutput($movie['title']) ?></h1>
        <p class="text-gray-600 mb-6">
            Directed by <strong><?= sanitizeOutput($movie['director_name']) ?></strong>
        </p>
        
        <!-- Description -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4">Overview</h2>
            <p class="text-gray-700 leading-relaxed">
                <?= nl2br(sanitizeOutput($movie['description'])) ?>
            </p>
        </div>
        
        <!-- Director -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4">About the Director</h2>
            <h3 class="font-semibold text-lg"><?= sanitizeOutput($movie['director_name']) ?></h3>
            <p class="text-gray-700 mt-2 leading-relaxed">
                <?= nl2br(sanitizeOutput($movie['director_biography'])) ?>
            </p>
        </div>
        
        <!-- Cast -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Cast</h2>
            
            <?php if (empty($actors)): ?>
                <p class="text-gray-500">No cast information available.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($actors as $actor): ?>
                        <div class="flex items-start p-3 border rounded-lg">
                            <div class="ml-3">
                                <h4 class="font-semibold"><?= sanitizeOutput($actor['name']) ?></h4>
                                <p class="text-gray-600 text-sm">as <?= sanitizeOutput($actor['role']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 
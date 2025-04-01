<?php
$pageTitle = ucfirst($category['name']) . ' Movies';
ob_start();
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold mb-4"><?= sanitizeOutput($category['name']) ?> Movies</h1>
    <p class="text-gray-600"><?= sanitizeOutput($category['description']) ?></p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($movies as $movie): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="<?= SITE_URL ?>/uploads/posters/<?= $movie['poster_url'] ?>" 
                 alt="<?= sanitizeOutput($movie['title']) ?>"
                 class="w-full h-64 object-cover">
            <div class="p-4">
                <h2 class="text-lg font-semibold mb-2">
                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" 
                       class="hover:text-blue-600">
                        <?= sanitizeOutput($movie['title']) ?>
                    </a>
                </h2>
                <p class="text-gray-600 mb-2">Directed by 
                    <a href="<?= SITE_URL ?>/director.php?id=<?= $movie['director_id'] ?>" 
                       class="hover:text-blue-600">
                        <?= sanitizeOutput($movie['director_name']) ?>
                    </a>
                </p>
                <p class="text-gray-600 mb-4"><?= $movie['release_year'] ?></p>
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

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?> 
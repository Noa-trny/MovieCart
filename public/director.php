<?php
$pageTitle = 'Films par réalisateur';

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$directorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn = connectDB();
$stmt = $conn->prepare("SELECT id, name, bio FROM directors WHERE id = ?");
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

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="javascript:history.back()" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
        <i class="fas fa-arrow-left mr-1"></i> Retour
    </a>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-5">
            <h1 class="text-2xl font-bold mb-3"><?= htmlspecialchars($director['name']) ?></h1>
            <?php if (isset($director['bio']) && !empty($director['bio'])): ?>
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($director['bio'])) ?></p>
            <?php else: ?>
                <p class="text-gray-500 italic">Aucune biographie disponible pour ce réalisateur.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <h2 class="text-xl font-bold mb-4">Films de <?= htmlspecialchars($director['name']) ?></h2>
    
    <?php if (empty($movies)): ?>
        <div class="bg-gray-100 p-6 rounded text-center">
            <i class="fas fa-film text-4xl text-gray-400 mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun film disponible</h3>
            <p class="text-gray-600">Nous n'avons pas encore de films de ce réalisateur dans notre catalogue.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach ($movies as $movie): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
                        <img src="<?= isset($movie['poster_path']) ? $movie['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" 
                             alt="<?= htmlspecialchars($movie['title']) ?>" 
                             class="w-full h-56 object-cover">
                    </a>
                    <div class="p-4">
                        <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" class="block">
                            <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($movie['title']) ?></h3>
                        </a>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-lg font-bold text-blue-600"><?= number_format($movie['price'], 2) ?> €</span>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?= SITE_URL ?>/add-to-cart.php?id=<?= $movie['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i> Ajouter
                                </a>
                            <?php else: ?>
                                <a href="<?= SITE_URL ?>/login.php" class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-300 transition-colors">
                                    <i class="fas fa-lock mr-1"></i> Connexion
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
require_once __DIR__ . '/src/views/layouts/main.php';
?> 
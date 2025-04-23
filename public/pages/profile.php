<?php
$pageTitle = 'Mon profil';
$customCss = '/css/profile.css';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';
require_once __DIR__ . '/../../src/models/Library.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    setFlashMessage('Vous devez être connecté pour accéder à votre profil.', 'error');
    redirectTo(SITE_URL . '/login.php');
}

$userId = $_SESSION['user_id'];
$conn = connectDB();

$stmt = $conn->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    setFlashMessage('Utilisateur non trouvé. Veuillez vous connecter à nouveau.', 'error');
    redirectTo(SITE_URL . '/logout.php');
}

$passwordMessage = '';
$passwordStatus = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $passwordMessage = 'Tous les champs sont obligatoires.';
        $passwordStatus = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $passwordMessage = 'Le nouveau mot de passe et la confirmation ne correspondent pas.';
        $passwordStatus = 'error';
    } elseif (strlen($newPassword) < 8) {
        $passwordMessage = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        $passwordStatus = 'error';
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();
        
        if ($userData && password_verify($currentPassword, $userData['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            
            if ($stmt->execute()) {
                $passwordMessage = 'Votre mot de passe a été mis à jour avec succès.';
                $passwordStatus = 'success';
            } else {
                $passwordMessage = 'Une erreur est survenue lors de la mise à jour du mot de passe.';
                $passwordStatus = 'error';
            }
            
            $stmt->close();
        } else {
            $passwordMessage = 'Le mot de passe actuel est incorrect.';
            $passwordStatus = 'error';
        }
    }
}

$library = new Library($userId);
$purchasedMovies = $library->getMovies();

$uniqueMovies = [];
foreach ($purchasedMovies as $movie) {
    if (!isset($uniqueMovies[$movie['id']])) {
        $uniqueMovies[$movie['id']] = $movie;
    }
}
$libraryMovies = array_values($uniqueMovies);
usort($libraryMovies, function($a, $b) {
    return strcmp($a['title'], $b['title']);
});

ob_start();
?>

<div class="profile-container">
    <div class="profile-header">
        <h1 class="profile-title">Mon profil</h1>
        <div class="profile-header-info">
            <span class="profile-email"><?= htmlspecialchars($user['email']) ?></span>
            <a href="<?= SITE_URL ?>/logout.php" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
    
    <div class="profile-grid">
        <div class="profile-card info-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">Informations personnelles</h2>
            </div>
            <div class="profile-card-body" style="padding: 2rem;">
                <div class="form-group" style="margin-bottom: 2rem; padding-bottom: 1.8rem; border-bottom: 1px solid #e5e7eb;">
                    <label class="form-label" style="margin-bottom: 0.8rem; font-size: 0.95rem;">Email</label>
                    <div style="font-size: 1.1rem; padding: 0.5rem 0; color: #3b82f6;"><?= htmlspecialchars($user['email']) ?></div>
                </div>
                
                <div class="form-group" style="margin-bottom: 2rem; padding-bottom: 1.8rem; border-bottom: 1px solid #e5e7eb;">
                    <label class="form-label" style="margin-bottom: 0.8rem; font-size: 0.95rem;">Nom d'utilisateur</label>
                    <div style="font-size: 1.2rem; padding: 0.5rem 0; font-weight: 600; color: #1f2937;"><?= htmlspecialchars($user['username']) ?></div>
                </div>
                
                <div class="form-group" style="margin-bottom: 0; padding-bottom: 0;">
                    <label class="form-label" style="margin-bottom: 0.8rem; font-size: 0.95rem;">Membre depuis</label>
                    <div style="font-size: 0.95rem; padding: 0.5rem 0; color: #10b981; font-style: italic;"><?= isset($user['created_at']) ? (new DateTime($user['created_at']))->format('d/m/Y') : 'Date non disponible' ?></div>
                </div>
            </div>
        </div>
        
        <div class="profile-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">Changer de mot de passe</h2>
            </div>
            <div class="profile-card-body">
                <?php if (!empty($passwordMessage)): ?>
                    <div class="form-message <?= $passwordStatus === 'success' ? 'form-message-success' : 'form-message-error' ?>">
                        <?= $passwordMessage ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= SITE_URL ?>/profile.php">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="form-group">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        Mettre à jour le mot de passe
                    </button>
                </form>
            </div>
        </div>
        
        <div class="profile-card purchases-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">Mes achats récents <?= !empty($purchasedMovies) ? '(' . count($purchasedMovies) . ')' : '' ?></h2>
            </div>
            <div class="profile-card-body" style="padding: 15px;">
                <div id="purchased-movies" style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                    <?php if (empty($purchasedMovies)): ?>
                        <div class="purchases-empty">
                            <i class="fas fa-film purchases-empty-icon"></i>
                            <p class="purchases-empty-text">Vous n'avez pas encore acheté de films.</p>
                            <a href="<?= SITE_URL ?>/" class="btn btn-primary discover-btn">
                                <i class="fas fa-search"></i> Découvrir des films
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                            <?php foreach ($purchasedMovies as $movie): ?>
                                <div style="display: flex; background-color: white; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                    <div style="width: 80px; flex-shrink: 0;">
                                        <img 
                                            src="<?= !empty($movie['poster_path']) ? $movie['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" 
                                            alt="<?= htmlspecialchars($movie['title']) ?>" 
                                            style="width: 100%; height: 120px; object-fit: cover;"
                                        >
                                    </div>
                                    <div style="padding: 12px; flex-grow: 1;">
                                        <h3 style="font-size: 1.1rem; font-weight: 600; margin: 0 0 8px 0;">
                                            <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" style="color: #1f2937; text-decoration: none; transition: color 0.3s;">
                                                <?= htmlspecialchars($movie['title']) ?>
                                            </a>
                                        </h3>
                                        <?php if (!empty($movie['director_name'])): ?>
                                            <p style="color: #6b7280; font-size: 0.9rem; margin: 0 0 5px 0;">
                                                Réalisé par <?= htmlspecialchars($movie['director_name']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($movie['purchase_date'])): ?>
                                            <p style="color: #10b981; font-size: 0.85rem; font-weight: 500; margin: 0;">
                                                Acheté le <?= (new DateTime($movie['purchase_date']))->format('d/m/Y') ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>  
        <div class="profile-card library-card" style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); overflow: hidden; height: fit-content; width: 100%; max-width: 100%;">
            <div class="profile-card-header" style="background-color: #f3f4f6; padding: 15px 20px; border-bottom: 1px solid #e5e7eb;">
                <h2 class="profile-card-title" style="font-size: 1.2rem; font-weight: bold; color: #1f2937; margin: 0;">
                    Ma bibliothèque <?= !empty($libraryMovies) ? '(' . count($libraryMovies) . ')' : '' ?>
                </h2>
            </div>
            <div class="profile-card-body" style="padding: 15px;">
                <?php if (empty($libraryMovies)): ?>
                    <div style="text-align: center; padding: 30px 0;">
                        <i class="fas fa-film" style="font-size: 2.5rem; color: #9ca3af; margin-bottom: 10px;"></i>
                        <p style="color: #6b7280;">Votre bibliothèque est vide.</p>
                        <a href="<?= SITE_URL ?>/" class="btn btn-primary" style="margin-top: 10px;">
                            <i class="fas fa-search"></i> Découvrir des films
                        </a>
                    </div>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; max-height: 350px; overflow-y: auto; padding: 5px;">
                        <?php foreach ($libraryMovies as $movie): ?>
                            <div style="width: 100%; margin: 0; padding: 0; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease; position: relative;">
                                <a href="<?= SITE_URL ?>/movie.php?id=<?= $movie['id'] ?>" style="display: block; width: 100%; position: relative; padding-bottom: 150%;">
                                    <img 
                                        src="<?= !empty($movie['poster_path']) ? $movie['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" 
                                        alt="<?= htmlspecialchars($movie['title']) ?>" 
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                    >
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$conn->close();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
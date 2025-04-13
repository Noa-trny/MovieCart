<?php
$pageTitle = 'Mon profil';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    setFlashMessage('Vous devez être connecté pour accéder à votre profil.', 'error');
    redirectTo(SITE_URL . '/login.php');
}

$userId = $_SESSION['user_id'];
$conn = connectDB();

$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
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

$tableExists = false;
$tableExistsQuery = "SELECT 1 FROM information_schema.tables 
                    WHERE table_schema = '" . DB_NAME . "' 
                    AND table_name = 'purchases'";
$tableResult = $conn->query($tableExistsQuery);
if ($tableResult && $tableResult->num_rows > 0) {
    $tableExists = true;
}

$purchasedMovies = [];
if ($tableExists) {
    $purchasedMovies = getUserPurchases($userId);
}

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
        <div class="profile-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">Informations personnelles</h2>
            </div>
            <div class="profile-card-body">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div><?= htmlspecialchars($user['email']) ?></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <div><?= !empty($user['firstname']) ? htmlspecialchars($user['firstname']) : 'Non renseigné' ?></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <div><?= !empty($user['lastname']) ? htmlspecialchars($user['lastname']) : 'Non renseigné' ?></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Membre depuis</label>
                    <div><?= (new DateTime($user['created_at']))->format('d/m/Y') ?></div>
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
        
        <div class="profile-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">Mes achats</h2>
            </div>
            <div class="profile-card-body">
                <div id="purchased-movies" class="purchased-movies">
                    <div class="purchases-loading">
                        <i class="fas fa-spinner fa-spin"></i> Chargement de votre bibliothèque...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchPurchasedMovies();
});

function fetchPurchasedMovies() {
    fetch('<?= SITE_URL ?>/../src/api/library.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            displayPurchasedMovies(data);
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('purchased-movies').innerHTML = `
                <div class="purchases-empty">
                    <i class="fas fa-exclamation-circle purchases-empty-icon"></i>
                    <p class="purchases-empty-text">Impossible de charger votre bibliothèque. Veuillez réessayer plus tard.</p>
                </div>
            `;
        });
}

function displayPurchasedMovies(movies) {
    const container = document.getElementById('purchased-movies');
    
    if (!movies || movies.length === 0) {
        container.innerHTML = `
            <div class="purchases-empty">
                <i class="fas fa-film purchases-empty-icon"></i>
                <p class="purchases-empty-text">Vous n'avez pas encore acheté de films.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    movies.forEach(movie => {
        html += `
            <div class="movie-item">
                <img 
                    src="${movie.poster_path || '<?= ASSETS_URL ?>/images/movie-placeholder.jpg'}" 
                    alt="${movie.title}" 
                    class="movie-item-poster"
                >
                <div class="movie-item-info">
                    <h3 class="movie-item-title">
                        <a href="<?= SITE_URL ?>/movie.php?id=${movie.id}">${movie.title}</a>
                    </h3>
                    ${movie.director_name ? `<p class="movie-item-director">Réalisé par ${movie.director_name}</p>` : ''}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}
</script>

<?php
$content = ob_get_clean();
$conn->close();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
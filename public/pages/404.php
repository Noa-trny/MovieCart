<?php
$pageTitle = 'Page non trouvée';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();
?>

<div style="text-align: center; padding: 40px 0;">
    <h1 style="font-size: 6rem; font-weight: bold; color: #3b82f6; margin-bottom: 20px;">404</h1>
    <h2 style="font-size: 2rem; font-weight: bold; margin-bottom: 15px;">Page non trouvée</h2>
    <p style="font-size: 1.1rem; color: #6b7280; margin-bottom: 30px;">La page que vous recherchez n'existe pas ou a été déplacée.</p>
    
    <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">
        <i class="fas fa-home"></i> Retour à l'accueil
    </a>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 
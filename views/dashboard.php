<?php

$pageTitle = 'Dashboard';
$pageDescription = 'Vue d’ensemble de votre infrastructure HAProxy.';
$currentPage = 'dashboard';

ob_start();

?>

<!-- Contenu HTML du dashboard -->
<section class="page-header">
    <h2 class="page-title">Vue d'ensemble</h2>
    <p class="page-description">
        Surveillez votre service HAProxy et vos serveurs.
    </p>
</section>

<?php

$content = ob_get_clean();

require __DIR__ . '/layouts/main.php';
<?php

$pageTitle = $pageTitle ?? 'Gestion HAProxy';
$pageDescription = $pageDescription ?? '';
$currentPage = $currentPage ?? 'dashboard';
$content = $content ?? '';

$pages = [
    'dashboard' => [
        'title' => 'Dashboard',
        'url' => '/',
        'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>'
    ],
    'servers' => [
        'title' => 'Serveurs',
        'url' => '/servers',
        'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="7" rx="2"/><rect x="3" y="13" width="18" height="7" rx="2"/><path d="M7 7.5h.01M7 16.5h.01M11 7.5h6M11 16.5h6"/></svg>'
    ],
    'service' => [
        'title' => 'Service HAProxy',
        'url' => '/service',
        'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83m8.48 8.48 2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48 2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>'
    ]
];

$pageCssFiles = [
    'dashboard' => 'dashboard.css',
    'servers' => 'servers.css',
    'service' => 'service.css'
];

$pageJsFiles = [
    'dashboard' => 'dashboard.js',
    'servers' => 'servers.js',
    'service' => 'service.js'
];

$pageCss = $pageCssFiles[$currentPage] ?? null;
$pageJs = $pageJsFiles[$currentPage] ?? null;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
        | Gestion HAProxy
    </title>

    <link rel="stylesheet" href="/assets/css/main.css">

    <?php if ($pageCss !== null): ?>
        <link rel="stylesheet" href="/assets/css/<?= htmlspecialchars($pageCss, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

</head>

<body>

    <div class="app-layout">

        <!-- Barre latérale -->
        <aside class="sidebar">

            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-text">HAProxy Manager</span>
                </div>
            </div>

            <nav class="sidebar-nav">

                <p class="nav-section-title">MENU PRINCIPAL</p>

                <?php foreach ($pages as $key => $page): ?>

                    <a
                        href="<?= htmlspecialchars($page['url'], ENT_QUOTES, 'UTF-8') ?>"
                        class="nav-link <?= $currentPage === $key ? 'active' : '' ?>"
                    >
                        <span class="nav-icon" aria-hidden="true">
                            <?= $page['icon'] ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </a>

                <?php endforeach; ?>

            </nav>

            <div class="sidebar-footer">
                <span class="connection-indicator"></span>
                <span>Interface d'administration</span>
            </div>

        </aside>


        <!-- Contenu principal -->
        <div class="main-wrapper">

            <header class="topbar">

                <div>
                    <h1 class="topbar-title">
                        <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <?php if ($pageDescription !== ''): ?>
                        <p class="topbar-description">
                            <?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="topbar-info">
                    <span class="topbar-status">
                        <span class="connection-indicator"></span>
                        Administration
                    </span>
                </div>

            </header>


            <main class="main-content">

                <div class="container">
                    <?= $content ?>
                </div>

            </main>


            <footer class="app-footer">
                <p>
                    Gestion HAProxy &copy; <?= date('Y') ?>
                    — Interface d'administration
                </p>
            </footer>

        </div>

    </div>


    <!-- Scripts communs -->
    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/main.js"></script>

    <!-- Script spécifique à la page -->
    <?php if ($pageJs !== null): ?>
        <script src="/assets/js/<?= htmlspecialchars($pageJs, ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endif; ?>

</body>

</html>
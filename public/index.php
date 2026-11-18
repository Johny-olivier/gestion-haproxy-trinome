<?php

use App\Core\Router;
use App\Controllers\ServerController;
use App\Controllers\ServiceController;
use App\Controllers\DashboardController;

// 1. chargement de l'autoloader

require_once __DIR__ . '/../vendor/autoload.php';

// 2. recup de l'url

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$requestUri = rtrim($requestUri, '/');

if ($requestUri === '') {
    $requestUri = '/';
}


// 3. gestion des fichiers statiques

// Permet au serveur PHP intégré de servir directement les fichiers CSS, JavaScript, images, etc.

if (PHP_SAPI === 'cli-server') {

    $requestedFile = realpath(__DIR__ . $requestUri);
    $publicDirectory = realpath(__DIR__);

    if (
        $requestedFile !== false
        && str_starts_with($requestedFile, $publicDirectory . DIRECTORY_SEPARATOR)
        && is_file($requestedFile)
    ) {
        return false;
    }
}


// 4. gestion des pages html

$pages = [
    '/' => 'dashboard.php',
    '/servers' => 'servers.php',
    '/service' => 'service.php'
];

if (isset($pages[$requestUri])) {

    $pageFile = __DIR__ . '/../views/' . $pages[$requestUri];

    if (is_file($pageFile)) {
        require $pageFile;
        exit;
    }

    http_response_code(500);
    echo 'Erreur : fichier de la page introuvable.';
    exit;
}


// 5. gestion de routes api

if (str_starts_with($requestUri, '/api')) {

    $router = new Router();

    // Gestion des serveurs

    $router->get('/api/servers', [ServerController::class, 'index']);
    $router->post('/api/servers', [ServerController::class, 'store']);
    $router->put('/api/servers/{id}', [ServerController::class, 'update']);
    $router->delete('/api/servers/{id}', [ServerController::class, 'destroy']);

    // Gestion du service HAProxy

    $router->get('/api/service/status', [ServiceController::class, 'status']);
    $router->post('/api/service/start', [ServiceController::class, 'start']);
    $router->post('/api/service/stop', [ServiceController::class, 'stop']);
    $router->post('/api/service/restart', [ServiceController::class, 'restart']);
    $router->post('/api/service/reload', [ServiceController::class, 'reload']);

    // Dashboard et Runtime API

    $router->get('/api/dashboard', [DashboardController::class, 'index']);
    $router->get('/api/runtime/servers', [DashboardController::class, 'runtimeServers']);
    $router->post('/api/runtime/servers/{id}/disable', [DashboardController::class, 'disableServer']);
    $router->post('/api/runtime/servers/{id}/enable', [DashboardController::class, 'enableServer']);

    // Exécution du routeur API
    $router->dispatch();

    exit;
}


// 6. PAGE 404

http_response_code(404);

echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>404 - Page introuvable</title>
</head>
<body>
    <h1>404 - Page introuvable</h1>
    <p>La page demandée n\'existe pas.</p>
    <a href="/">Retour au dashboard</a>
</body>
</html>';
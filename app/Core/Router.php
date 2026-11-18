<?php

namespace App\Core;
use App\Core\Response;

class Router {
    private array $routes = [];

    private function addRoute(string $method, string $path, array $handler): void {
        $this->routes[$method][$path] = $handler;
    }

    public function get(string $path, array $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array $handler): void {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, array $handler): void {
        $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Recherche une route correspondant à la requête.
     */
    private function findRoute(string $method, string $uri): ?array {
        foreach ($this->routes[$method] ?? [] as $path => $handler) {
            $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $path);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );

                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }

        return null;
    }

    /**
     * Instancie le contrôleur et exécute sa méthode.
     */
    private function executeHandler(array $handler, array $params): Response {
        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Contrôleur introuvable : $controllerClass");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Méthode introuvable : $method");
        }

        $response = $controller->$method(...array_values($params));

        if (!$response instanceof Response) {
            throw new \RuntimeException("Le contrôleur doit retourner un objet Response.");
        }

        return $response;
    }

    /**
     * Identifie et exécute la route demandée.
     */
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $uri = rtrim($uri, '/') ?: '/';

        $route = $this->findRoute($method, $uri);

        if ($route === null) {
            Response::json(null, 404, 'Route introuvable.')->send();
            return;
        }

        try {
            $response = $this->executeHandler(
                $route['handler'],
                $route['params']
            );

            $response->send();

        } catch (\Throwable $e) {
            Response::json(null, 500, 'Une erreur interne est survenue.')->send();

            // En développement, tu peux journaliser $e->getMessage().
            error_log($e->getMessage());
        }
    }
}
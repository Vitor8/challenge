<?php

require_once __DIR__ . '/core/AuthMiddleware.php';

/**
 * Handles incoming HTTP requests and routes them to the appropriate controller.
 */
class Router {
    private AuthMiddleware $authMiddleware;
    private array $routes;

    public function __construct() {
        $this->authMiddleware = new AuthMiddleware();
        $this->routes = require __DIR__ . '/routes.php';
    }

    /**
     * Processes the incoming request and dispatches it to the correct controller.
     */
    public function handleRequest(): void {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Serve static frontend files directly
        if ($this->serveStaticFiles($requestUri)) {
            return;
        }

        // Check authentication before processing routes
        $this->authMiddleware->checkAuthentication($requestUri);

        // Dispatch the route
        $this->dispatch($requestUri);
    }

    /**
     * Checks if the requested file exists in the frontend directory and serves it.
     *
     * @param string $requestUri The requested URI.
     * @return bool Returns true if the file exists and is served.
     */
    private function serveStaticFiles(string $requestUri): bool {
        $filePath = __DIR__ . "/frontend" . $requestUri;
        if (file_exists($filePath)) {
            return false;
        }
        return false;
    }

    /**
     * Dispatches the request to the appropriate controller and method.
     *
     * @param string $requestUri The requested URI.
     */
    private function dispatch(string $requestUri): void {
        if (!array_key_exists($requestUri, $this->routes)) {
            $this->sendNotFoundResponse();
            return;
        }

        [$controllerName, $method] = $this->routes[$requestUri];
        $controllerFile = __DIR__ . "/controllers/$controllerName.php";

        if (!file_exists($controllerFile)) {
            $this->sendNotFoundResponse();
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            $this->sendNotFoundResponse();
            return;
        }

        $controllerInstance = new $controllerName();

        if (!method_exists($controllerInstance, $method)) {
            $this->sendNotFoundResponse();
            return;
        }

        $response = $controllerInstance->$method();

        if ($response !== null) {
            echo $response;
        }
        exit;
    }

    /**
     * Sends a 404 response when the requested resource is not found.
     */
    private function sendNotFoundResponse(): void {
        http_response_code(404);
        echo "404 - Página não encontrada";
    }
}

// Initialize and handle request
$router = new Router();
$router->handleRequest();

<?php

require_once __DIR__ . '/core/AuthMiddleware.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (file_exists(__DIR__ . "/frontend" . $requestUri)) {
    return false;
}

$authMiddleware = new AuthMiddleware();
$authMiddleware->checkAuthentication($requestUri);

$routes = require 'routes.php';

if (array_key_exists($requestUri, $routes)) {
    [$controllerName, $method] = $routes[$requestUri];

    $controllerFile = __DIR__ . "/controllers/$controllerName.php";

    if (file_exists($controllerFile)) {
        require_once $controllerFile;

        if (class_exists($controllerName)) {
            $controllerInstance = new $controllerName();
            
            if (method_exists($controllerInstance, $method)) {
                $response = $controllerInstance->$method();
                
                if ($response !== null) {
                    echo $response;
                }
                exit;
            }
        }
    }
}

http_response_code(404);
echo "404 - Página não encontrada";

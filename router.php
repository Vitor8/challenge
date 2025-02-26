<?php

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (file_exists(__DIR__ . "/frontend" . $requestUri)) {
    return false;
}

$routes = require 'routes.php';

if (array_key_exists($requestUri, $routes)) {
    [$controllerName, $method] = $routes[$requestUri];

    require_once __DIR__ . "/controllers/$controllerName.php";

    $controller = new $controllerName();
    
    $response = $controller->$method();

    if ($response !== null) {
        echo $response;
    }
} else {
    http_response_code(404);
    echo "404 - Página não encontrada";
}

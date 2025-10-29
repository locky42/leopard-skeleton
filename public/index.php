<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Leopard\Core\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$psr17Factory = new Psr17Factory();
global $container;

$container->set('debug', function () {
    return new \Leopard\Core\Helpers\Debug();
});

$container->set('config.routes', function () {
    return new \Leopard\Core\Services\Config();
});

$container->get('config.routes')->load(__DIR__ . '/../config/routes.yaml');

$router = new Router($container);

// Load routes and controllers
$router->loadConfig(
    $container->get('config.routes')->getAll()
);
$router->loadControllersFrom(__DIR__ . '/../src/Controllers');

// Create a PSR-7 ServerRequest object using ServerRequestCreator
$serverRequestCreator = new ServerRequestCreator(
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory
);
$request = $serverRequestCreator->fromGlobals();

// Додаємо CORS заголовки
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Обробляємо preflight OPTIONS запит
if ($request->getMethod() === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Dispatch the request and get the response
$response = $router->dispatch($request->getMethod(), $request->getUri()->getPath());

http_response_code($container->get('response')->getStatusCode());
foreach ($container->get('response')->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $container->get('response')->getBody();

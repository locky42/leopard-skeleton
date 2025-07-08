<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$psr17Factory = new Psr17Factory();
global $container;
$container = new Container();

$container->set('debug', function () {
    return new \App\Core\Helpers\Debug();
});

$container->set('params', function () {
    return new \App\Core\Services\Params();
});

$container->get('params')->load(__DIR__ . '/../config/app.php');

$container->set('config.routes', function () {
    return new \App\Core\Services\Config();
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

// Dispatch the request and get the response
$response = $router->dispatch($request->getMethod(), $request->getUri()->getPath());

http_response_code($container->get('response')->getStatusCode());
foreach ($container->get('response')->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $container->get('response')->getBody();

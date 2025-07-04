<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$psr17Factory = new Psr17Factory();
$container = new Container();
$router = new Router($container);

// Load routes and controllers
$router->loadRoutesFromYaml(__DIR__ . '/../config/routes.yaml');
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

// Send the response
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $response->getBody();

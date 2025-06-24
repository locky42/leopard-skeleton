<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Container;

$container = new Container();
$router = new Router($container);

$router->loadRoutesFromYaml(__DIR__ . '/../config/routes.yaml');
$router->loadControllersFrom(__DIR__ . '/../src/Controllers');

$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

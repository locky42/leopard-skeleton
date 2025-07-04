<?php

use PHPUnit\Framework\TestCase;
use App\Core\Router;
use App\Core\Container;

class RouterTest extends TestCase
{
    public function testDispatchNotFound(): void
    {
        $container = new Container();
        $router = new Router($container);

        $this->expectOutputString('404 Not Found');
        $router->dispatch('GET', '/non-existent-route');
    }

    public function testDispatchExistingRoute(): void
    {
        $container = new Container();
        $router = new Router($container);

        $router->registerController(App\Controllers\HomeController::class);

        $this->expectOutputString('Hello from HomeController::index');
        $router->dispatch('GET', '/');
    }
}

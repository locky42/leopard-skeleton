<?php

namespace App\Core;

use App\Core\Attributes\Route as RouteAttribute;
use Symfony\Component\Yaml\Yaml;
use ReflectionClass;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Router
{
    private Container $container;
    private array $routes = [];
    private array $yamlRoutes = [];
    private array $yamlControllers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function loadRoutesFromYaml(string $yamlPath): void
    {
        $config = Yaml::parseFile($yamlPath);

        // Явні маршрути
        if (!empty($config['routes'])) {
            foreach ($config['routes'] as $route) {
                $controller = 'App\\Controllers\\' . $route['controller'];
                $key = $controller . '::' . $route['action'];
                $this->yamlRoutes[$key] = [
                    'method' => strtoupper($route['method']),
                    'path' => $route['path'],
                ];
            }
        }

        // Базові шляхи контролерів
        if (!empty($config['controllers'])) {
            foreach ($config['controllers'] as $entry) {
                $controller = 'App\\Controllers\\' . $entry['controller'];
                $this->yamlControllers[$controller] = $entry['path'] ?? null;
            }
        }
    }

    public function loadControllersFrom(string $dir): void
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), 'Controller.php')) {
                $class = $this->convertPathToClass($file->getPathname());
                if (class_exists($class)) {
                    $this->registerController($class);
                } else {
                    throw new \RuntimeException("Controller class $class not found in file: " . $file->getPathname());
                }
            }
        }
    }

    private function convertPathToClass(string $path): string
    {
        $relative = str_replace([$_SERVER['DOCUMENT_ROOT'] . '/../src/Controllers/', '.php'], '', $path);
        $namespace = str_replace('/', '\\', $relative);
        return 'App\\Controllers\\' . $namespace;
    }

    public function registerController(string $controllerClass): void
    {
        $refClass = new ReflectionClass($controllerClass);

        foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor()) continue;

            $methodName = $method->getName();
            $routePath = null;
            $routeMethod = 'GET';

            // 1. Атрибут #[Route]
            foreach ($method->getAttributes(RouteAttribute::class) as $attr) {
                /** @var RouteAttribute $route */
                $route = $attr->newInstance();
                $routePath = $route->path;
                $routeMethod = strtoupper($route->method);
                break;
            }

            // 2. YAML routes:
            if (!$routePath) {
                $key = $controllerClass . '::' . $methodName;
                if (isset($this->yamlRoutes[$key])) {
                    $routePath = $this->yamlRoutes[$key]['path'];
                    $routeMethod = $this->yamlRoutes[$key]['method'];
                }
            }

            // 3. YAML controllers:
            if (!$routePath) {
                if (array_key_exists($controllerClass, $this->yamlControllers)) {
                    $basePath = $this->yamlControllers[$controllerClass];
                    if (empty($basePath)) {
                        $basePath = $this->namespaceToPath($controllerClass);
                    }
                } else {
                    $basePath = $this->namespaceToPath($controllerClass);
                }

                // обробка index()
                if (strtolower($methodName) === 'index') {
                    $routePath = ($basePath === '/' || $basePath === '') ? '/' : $basePath;
                } else {
                    $routePath = rtrim($basePath, '/') . '/' . strtolower($methodName);
                }
            }

            $this->routes[] = [
                'method' => $routeMethod,
                'path' => $routePath,
                'controller' => $controllerClass,
                'action' => $methodName,
            ];
        }
    }

    private function namespaceToPath(string $class): string
    {
        $trimmed = str_replace('App\\Controllers\\', '', $class);
        $segments = explode('\\', $trimmed);
        $segments = array_map(fn($s) => strtolower(preg_replace('/Controller$/', '', $s)), $segments);
        return '/' . implode('/', $segments);
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && $route['path'] === $uri) {
                $controller = $this->container->get($route['controller']);
                call_user_func([$controller, $route['action']]);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}

<?php

namespace App\Core;

use App\Core\Attributes\Route as RouteAttribute;
use Symfony\Component\Yaml\Yaml;
use ReflectionClass;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

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

            list($regex, $params) = $this->compilePath($routePath);

            $this->routes[] = [
                'method' => $routeMethod,
                'path' => $routePath,
                'regex' => $regex,
                'params' => $params,
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

    public function dispatch(string $method, string $uri): ResponseInterface
    {
        $uri = rtrim($uri, '/') ?: '/';
        $psr17Factory = new Psr17Factory();

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                array_shift($matches); // Remove full match

                $params = [];
                foreach ($route['params'] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }

                $controller = $this->container->get($route['controller']);
                $refMethod = new \ReflectionMethod($controller, $route['action']);
                $args = [];
                foreach ($refMethod->getParameters() as $param) {
                    $name = $param->getName();
                    $type = $param->getType()?->getName();

                    if (!array_key_exists($name, $params)) {
                        $args[] = null;
                        continue;
                    }

                    $value = $params[$name];

                    // Type conversion
                    if ($type === 'int') {
                        if (!ctype_digit($value)) {
                            return $this->createErrorResponse($psr17Factory, 404, "404 Not Found (invalid int: $name)");
                        }
                        $value = (int) $value;
                    } elseif ($type === 'float') {
                        if (!is_numeric($value)) {
                            return $this->createErrorResponse($psr17Factory, 404, "404 Not Found (invalid float: $name)");
                        }
                        $value = (float) $value;
                    } elseif ($type === 'bool') {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        if ($value === null) {
                            return $this->createErrorResponse($psr17Factory, 404, "404 Not Found (invalid bool: $name)");
                        }
                    } elseif ($type !== null && $type !== 'string') {
                        return $this->createErrorResponse($psr17Factory, 500, "Unsupported parameter type: $type");
                    }

                    $args[] = $value;
                }

                $response = $psr17Factory->createResponse(200);
                $responseBody = $refMethod->invokeArgs($controller, $args);

                // Ensure the response body is a string
                $responseBody = $responseBody ?? ''; // Default to an empty string if null
                $response->getBody()->write((string)$responseBody);
                return $response;
            }
        }

        return $this->createErrorResponse($psr17Factory, 404, "404 Not Found");
    }

    private function createErrorResponse(Psr17Factory $factory, int $statusCode, string $message): ResponseInterface
    {
        $response = $factory->createResponse($statusCode);
        $response->getBody()->write($message);
        return $response;
    }

    private function compilePath(string $path): array
    {
        $paramNames = [];
        $regex = preg_replace_callback('#\{([^}]+)\}#', function ($matches) use (&$paramNames) {
            $paramNames[] = $matches[1];
            return '([^/]+)';
        }, $path);
        
        $regex = '#^' . $regex . '$#';
        return [$regex, $paramNames];
    }
}

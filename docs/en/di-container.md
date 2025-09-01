# Dependency Injection Container

## Overview
The `Container` is a class for managing dependencies, allowing you to register services and retrieve their instances. It implements the principles of Dependency Injection (DI) to simplify working with dependencies.

## Core Methods of the Container

### Registering a Service
```php
$container->set(Parsedown::class, function () {
    return new Parsedown();
});
```
- **`set`**: Registers a service in the container. You provide a unique identifier (`Parsedown::class`) and a function that creates the service instance.

### Retrieving a Service
```php
$parsedown = $container->get(Parsedown::class);
echo $parsedown->text('Hello, _Parsedown_!');
```
- **`get`**: Returns the instance of a service by its identifier. If the service has not been created yet, the container creates it using the function provided in `set`.

### Checking for a Service
```php
if ($container->has(Parsedown::class)) {
    $parsedown = $container->get(Parsedown::class);
}
```
- **`has`**: Checks whether a service is registered or if a class with the given identifier exists.

## Example Usage in a Controller

```php
namespace App\Controllers\Site;

class HomeController extends HtmlController
{
    public function index(): string
    {
        $parsedown = $this->get(Parsedown::class);
        $markdown = file_get_contents(__DIR__ . '/../../../README.md');
        $documentation = $parsedown->text($markdown);

        return $this->view->render('home', [
            'title' => 'Leopard Framework',
            'documentation' => $documentation
        ]);
    }
}
```

## Testing the Container
The container is tested in the file `tests/Core/ContainerTest.php`. For example:

```php
public function testGetInstance(): void
{
    $container = new Container();
    $instance = $container->get(stdClass::class);

    $this->assertInstanceOf(stdClass::class, $instance);
}
```

## Benefits of Using the Container
- **Easy Service Registration**: You can register any classes or functions.
- **Lazy Loading**: Services are created only when they are needed.
- **Flexibility**: The container makes it easy to manage dependencies in your project.

# Dynamic Routes

## Route with Parameter Types
```php
#[Route('/calculate/{a}/{b}', method: 'GET')]
public function calculate(int $a, int $b): string
{
    return "Sum: " . ($a + $b);
}
```
- **Parameters**: `{a}` and `{b}` are automatically converted to `int` type.
- **Result**: Returns the text "Sum: {a + b}".

---

## Route with Boolean Parameter
```php
#[Route('/toggle/{status}', method: 'GET')]
public function toggle(bool $status): string
{
    return $status ? "Enabled" : "Disabled";
}
```
- **Parameter**: `{status}` is automatically converted to `bool` type.
- **Result**: Returns the text "Enabled" or "Disabled".

---

## Automatic Route Detection

### Overview
Leopard Skeleton supports automatic route detection through YAML configuration or by scanning controllers. This reduces the amount of manual work when adding new routes.

---

## How Automatic Route Detection Works

1. **Controller Scanning**:
   - The system automatically scans all controllers in the defined namespaces.
   - All methods with the `#[Route]` attribute are added to the routing.
   - Methods without the `#[Route]` attribute are also added to the routing, where the final route matches the method name.

2. **Controller Base Paths**:
   - In the YAML configuration, you can define a base path for a group of controllers.
   - This helps organize routes by namespaces.

3. **Dynamic Parameters**:
   - Parameters in routes, such as `{id}`, are automatically passed to the controller method.

---

## YAML Configuration for Automatic Route Detection

```yaml
controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```
- **`namespace`**: The controller namespace that defines the group of routes.
- **`path`**: The base path for all methods in the controller.

---

## Example of Automatic Route Detection

### Controller:
```php
namespace App\Controllers\Site;

use App\Core\Attributes\Route;

class UserController
{
    #[Route('/user/{id}', method: 'GET')]
    public function getUser(int $id): string
    {
        return "User ID: $id";
    }

    #[Route('/user/{id}/delete', method: 'DELETE')]
    public function deleteUser(int $id): string
    {
        return "User $id deleted!";
    }
}
```

### YAML Configuration:
```yaml
controllers:
  - namespace: Site
    path: /site
```

### Result:
- **Path**: `/site/user/{id}`
- **Method**: `GET`
- **Result**: Returns the text "User ID: {id}".

- **Path**: `/site/user/{id}/delete`
- **Method**: `DELETE`
- **Result**: Returns the text "User {id} deleted!".

---

## Advantages of Automatic Route Detection
- **Reduced Manual Work**: No need to manually add routes to the configuration.
- **Flexibility**: Easily organize routes by namespaces.
- **Dynamic**: Supports parameters and various HTTP methods.

---

# Other Examples

## Route for Static Pages
```php
#[Route('/about', method: 'GET')]
public function about(): string
{
    return $this->view->render('site/about', ['title' => 'About Us']);
}
```
- **Result**: Renders the `about.php` template.

---

## Route for API
```php
#[Route('/api/user/{id}', method: 'GET')]
public function getUserApi(int $id): array
{
    return ['id' => $id, 'name' => 'John Doe'];
}
```
- **Result**: Returns JSON data.

---

## Route for File Download
```php
#[Route('/download/{file}', method: 'GET')]
public function download(string $file): ResponseInterface
{
    $response = new Psr17Factory()->createResponse(200);
    $response->getBody()->write(file_get_contents(__DIR__ . "/files/$file"));
    return $response;
}
```
- **Result**: Downloads a file from the server.

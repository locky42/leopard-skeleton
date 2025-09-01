
# Routing via YAML

## Route Configuration
```yaml
routes:
  - controller: Site\HomeController
    action: home
    method: GET
    path: /home

  - controller: Site\UserController
    action: getUser
    method: GET
    path: /user/{id}

  - controller: Site\UserController
    action: deleteUser
    method: DELETE
    path: /user/{id}/delete
```
- **`controller`**: The name of the controller that defines the class handling the route.
- **`action`**: The controller method that will be called to handle the route.
- **`method`**: The HTTP method supported by the route (e.g., `GET`, `POST`, `DELETE`).
- **`path`**: The route path, which can include dynamic parameters like `{id}`.

---

## Base Path for Controllers
```yaml
controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```
- **`namespace`**: The namespace of the controller, which defines a group of routes.
- **`path`**: The base path for all methods of the controller. For example, all methods of the `Admin` controller will be accessible via paths starting with `/admin`.

---

## Example YAML File
```yaml
routes:
  - controller: Site\HomeController
    action: home
    method: GET
    path: /home

  - controller: Site\UserController
    action: getUser
    method: GET
    path: /user/{id}

  - controller: Site\UserController
    action: updateUser
    method: PATCH
    path: /user/{id}/update

controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```

---

## How Routing via YAML Works
1. **Explicit Routes**:
   - Defined in the `routes` section.
   - Each route has a `controller`, `action`, `method`, and `path`.

2. **Base Paths for Controllers**:
   - Defined in the `controllers` section.
   - Add a base path for all methods of the controller.

3. **Dynamic Parameters**:
   - Parameters in routes, such as `{id}`, are automatically passed to the controller method.

---

## Usage Example
### YAML Configuration:
```yaml
routes:
  - controller: Site\UserController
    action: getUser
    method: GET
    path: /user/{id}
```

### Controller:
```php
namespace App\Controllers\Site;

class UserController
{
    public function getUser(int $id): string
    {
        return "User ID: $id";
    }
}
```

### Result:
- **Path**: `/user/123`
- **Result**: Returns the text "User ID: 123".

---

This documentation now covers all aspects of routing via YAML, including explicit routes, base paths for controllers, and dynamic parameters.

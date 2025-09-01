# Routing with Attributes `#[Route]`

## Simple Route
```php
#[Route('/home', method: 'GET')]
public function home(): string
{
    return "Welcome to the Home Page!";
}
```
- **Path**: `/home`
- **HTTP Method**: `GET`
- **Result**: Returns the text "Welcome to the Home Page!".

---

## Route with Parameters
```php
#[Route('/user/{id}', method: 'GET')]
public function getUser(int $id): string
{
    return "User ID: $id";
}
```
- **Path**: `/user/{id}`
- **Parameter**: `{id}` is automatically converted to type `int`.
- **Result**: Returns the text "User ID: {id}".

---

## Route with Multiple Parameters
```php
#[Route('/product/{category}/{id}', method: 'GET')]
public function getProduct(string $category, int $id): string
{
    return "Category: $category, Product ID: $id";
}
```
- **Path**: `/product/{category}/{id}`
- **Parameters**: `{category}` and `{id}`
- **Result**: Returns the text "Category: {category}, Product ID: {id}".

---

## Route for POST Request
```php
#[Route('/form/submit', method: 'POST')]
public function submitForm(): string
{
    return "Form submitted successfully!";
}
```
- **HTTP Method**: `POST`
- **Result**: Returns the text "Form submitted successfully!".

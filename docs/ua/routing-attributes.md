# Маршрутизація через атрибути `#[Route]`



## Простий маршрут
```php
#[Route('/home', method: 'GET')]
public function home(): string
{
    return "Welcome to the Home Page!";
}
```
- **Шлях**: `/home`
- **HTTP-метод**: `GET`
- **Результат**: Повертає текст "Welcome to the Home Page!".

---

## Маршрут з параметрами
```php
#[Route('/user/{id}', method: 'GET')]
public function getUser(int $id): string
{
    return "User ID: $id";
}
```
- **Шлях**: `/user/{id}`
- **Параметр**: `{id}` автоматично конвертується в тип `int`.
- **Результат**: Повертає текст "User ID: {id}".

---

## Маршрут з кількома параметрами
```php
#[Route('/product/{category}/{id}', method: 'GET')]
public function getProduct(string $category, int $id): string
{
    return "Category: $category, Product ID: $id";
}
```
- **Шлях**: `/product/{category}/{id}`
- **Параметри**: `{category}` і `{id}`
- **Результат**: Повертає текст "Category: {category}, Product ID: {id}".

---

## Маршрут для POST-запиту
```php
#[Route('/form/submit', method: 'POST')]
public function submitForm(): string
{
    return "Form submitted successfully!";
}
```
- **HTTP-метод**: `POST`
- **Результат**: Повертає текст "Form submitted successfully!".

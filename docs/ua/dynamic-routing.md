# Динамічні маршрути

## Маршрут з типами параметрів
```php
#[Route('/calculate/{a}/{b}', method: 'GET')]
public function calculate(int $a, int $b): string
{
    return "Sum: " . ($a + $b);
}
```
- **Параметри**: `{a}` і `{b}` автоматично конвертуються в тип `int`.
- **Результат**: Повертає текст "Sum: {a + b}".

---

## Маршрут з булевим параметром
```php
#[Route('/toggle/{status}', method: 'GET')]
public function toggle(bool $status): string
{
    return $status ? "Enabled" : "Disabled";
}
```
- **Параметр**: `{status}` автоматично конвертується в тип `bool`.
- **Результат**: Повертає текст "Enabled" або "Disabled".

---

## Автоматичне визначення маршрутів

### Огляд
Leopard Skeleton підтримує автоматичне визначення маршрутів через конфігурацію YAML або шляхом сканування контролерів. Це дозволяє зменшити кількість ручної роботи при додаванні нових маршрутів.

---

## Як працює автоматичне визначення маршрутів

1. **Сканування контролерів**:
   - Система автоматично сканує всі контролери у визначених просторах імен.
   - Всі методи з атрибутом `#[Route]` додаються до маршрутизації.
   - Методи без атрибута `#[Route]` теж додаються в маршрутизацію, де кінцевий маршрут буде такий, як і назва метода

2. **Базові шляхи контролерів**:
   - У конфігурації YAML можна визначити базовий шлях для групи контролерів.
   - Це дозволяє організувати маршрути за просторами імен.

3. **Динамічні параметри**:
   - Параметри в маршрутах, наприклад `{id}`, автоматично передаються в метод контролера.

---

## Конфігурація YAML для автоматичного визначення маршрутів

```yaml
controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```
- **`namespace`**: Простір імен контролера, який визначає групу маршрутів.
- **`path`**: Базовий шлях для всіх методів контролера.

---

## Приклад автоматичного визначення маршрутів

### Контролер:
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

### Конфігурація YAML:
```yaml
controllers:
  - namespace: Site
    path: /site
```

### Результат:
- **Шлях**: `/site/user/{id}`
- **Метод**: `GET`
- **Результат**: Повертає текст "User ID: {id}".

- **Шлях**: `/site/user/{id}/delete`
- **Метод**: `DELETE`
- **Результат**: Повертає текст "User {id} deleted!".

---

## Переваги автоматичного визначення маршрутів
- **Зменшення ручної роботи**: Не потрібно вручну додавати маршрути в конфігурацію.
- **Гнучкість**: Легко організувати маршрути за просторами імен.
- **Динамічність**: Підтримка параметрів і різних HTTP-методів.

---

# Інші приклади

## Маршрут для статичних сторінок
```php
#[Route('/about', method: 'GET')]
public function about(): string
{
    return $this->view->render('site/about', ['title' => 'About Us']);
}
```
- **Результат**: Рендерить шаблон `about.php`.

---

## Маршрут для API
```php
#[Route('/api/user/{id}', method: 'GET')]
public function getUserApi(int $id): array
{
    return ['id' => $id, 'name' => 'John Doe'];
}
```
- **Результат**: Повертає JSON-дані.

---

## Маршрут для завантаження файлу
```php
#[Route('/download/{file}', method: 'GET')]
public function download(string $file): ResponseInterface
{
    $response = new Psr17Factory()->createResponse(200);
    $response->getBody()->write(file_get_contents(__DIR__ . "/files/$file"));
    return $response;
}
```
- **Результат**: Завантажує файл із сервера.

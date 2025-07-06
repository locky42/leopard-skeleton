# Leopard Skeleton

## Огляд
Leopard Skeleton — це базовий каркас для PHP-додатків, який підтримує MVC-архітектуру, роутинг, шаблони, блоки, тестування та інші сучасні функції. Проект побудований на PHP 8.3 і відповідає стандартам PSR.

---

## Маршрутизація через атрибути `#[Route]`

### Простий маршрут
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

### Маршрут з параметрами
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

### Маршрут з кількома параметрами
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

### Маршрут для POST-запиту
```php
#[Route('/form/submit', method: 'POST')]
public function submitForm(): string
{
    return "Form submitted successfully!";
}
```
- **HTTP-метод**: `POST`
- **Результат**: Повертає текст "Form submitted successfully!".

---

## Маршрутизація через YAML

### Конфігурація маршруту
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
- **`controller`**: Ім'я контролера, яке визначає клас, що обробляє маршрут.
- **`action`**: Метод контролера, який буде викликаний для обробки маршруту.
- **`method`**: HTTP-метод, який підтримується маршрутом (наприклад, `GET`, `POST`, `DELETE`).
- **`path`**: Шлях до маршруту, який може включати динамічні параметри, наприклад `{id}`.

---

### Базовий шлях для контролера
```yaml
controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```
- **`namespace`**: Простір імен контролера, який визначає групу маршрутів.
- **`path`**: Базовий шлях для всіх методів контролера. Наприклад, всі методи контролера `Admin` будуть доступні за шляхами, що починаються з `/admin`.

---

### Приклад YAML-файлу
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

  - controller: Site\UserController
    action: deleteUser
    method: DELETE
    path: /user/{id}/delete

controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```

---

### Як працює маршрутизація через YAML
1. **Явні маршрути**:
   - Визначаються в секції `routes`.
   - Кожен маршрут має `controller`, `action`, `method` і `path`.

2. **Базові шляхи контролерів**:
   - Визначаються в секції `controllers`.
   - Додають базовий шлях для всіх методів контролера.

3. **Динамічні параметри**:
   - Параметри в маршрутах, наприклад `{id}`, автоматично передаються в метод контролера.

---

### Приклад використання
#### YAML-конфігурація:
```yaml
routes:
  - controller: Site\UserController
    action: getUser
    method: GET
    path: /user/{id}
```

#### Контролер:
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

#### Результат:
- **Шлях**: `/user/123`
- **Результат**: Повертає текст "User ID: 123".

---

Ця документація тепер охоплює всі аспекти маршрутизації через YAML, включаючи явні маршрути, базові шляхи контролерів і динамічні параметри.## Маршрутизація через YAML

### Конфігурація маршруту
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
- **`controller`**: Ім'я контролера, яке визначає клас, що обробляє маршрут.
- **`action`**: Метод контролера, який буде викликаний для обробки маршруту.
- **`method`**: HTTP-метод, який підтримується маршрутом (наприклад, `GET`, `POST`, `DELETE`).
- **`path`**: Шлях до маршруту, який може включати динамічні параметри, наприклад `{id}`.

---

### Базовий шлях для контролера
```yaml
controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```
- **`namespace`**: Простір імен контролера, який визначає групу маршрутів.
- **`path`**: Базовий шлях для всіх методів контролера. Наприклад, всі методи контролера `Admin` будуть доступні за шляхами, що починаються з `/admin`.

---

### Приклад YAML-файлу
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

  - controller: Site\UserController
    action: deleteUser
    method: DELETE
    path: /user/{id}/delete

controllers:
  - namespace: Site
    path: /site

  - namespace: Admin
    path: /admin
```

---

### Як працює маршрутизація через YAML
1. **Явні маршрути**:
   - Визначаються в секції `routes`.
   - Кожен маршрут має `controller`, `action`, `method` і `path`.

2. **Базові шляхи контролерів**:
   - Визначаються в секції `controllers`.
   - Додають базовий шлях для всіх методів контролера.

3. **Динамічні параметри**:
   - Параметри в маршрутах, наприклад `{id}`, автоматично передаються в метод контролера.

---

### Приклад використання
#### YAML-конфігурація:
```yaml
routes:
  - controller: Site\UserController
    action: getUser
    method: GET
    path: /user/{id}
```

#### Контролер:
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

#### Результат:
- **Шлях**: `/user/123`
- **Результат**: Повертає текст "User ID: 123".

---

## Динамічні маршрути

### Маршрут з типами параметрів
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

### Маршрут з булевим параметром
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

### Автоматичне визначення маршрутів

#### Огляд
Leopard Skeleton підтримує автоматичне визначення маршрутів через конфігурацію YAML або шляхом сканування контролерів. Це дозволяє зменшити кількість ручної роботи при додаванні нових маршрутів.

---

### Як працює автоматичне визначення маршрутів

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

### Конфігурація YAML для автоматичного визначення маршрутів

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

### Приклад автоматичного визначення маршрутів

#### Контролер:
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

#### Конфігурація YAML:
```yaml
controllers:
  - namespace: Site
    path: /site
```

#### Результат:
- **Шлях**: `/site/user/{id}`
- **Метод**: `GET`
- **Результат**: Повертає текст "User ID: {id}".

- **Шлях**: `/site/user/{id}/delete`
- **Метод**: `DELETE`
- **Результат**: Повертає текст "User {id} deleted!".

---

### Переваги автоматичного визначення маршрутів
- **Зменшення ручної роботи**: Не потрібно вручну додавати маршрути в конфігурацію.
- **Гнучкість**: Легко організувати маршрути за просторами імен.
- **Динамічність**: Підтримка параметрів і різних HTTP-методів.

---

Ця функція дозволяє значно спростити процес маршрутизації та забезпечує масштабованість проекту.

## Інші приклади

### Маршрут для статичних сторінок
```php
#[Route('/about', method: 'GET')]
public function about(): string
{
    return $this->view->render('site/about', ['title' => 'About Us']);
}
```
- **Результат**: Рендерить шаблон `about.php`.

---

### Маршрут для API
```php
#[Route('/api/user/{id}', method: 'GET')]
public function getUserApi(int $id): array
{
    return ['id' => $id, 'name' => 'John Doe'];
}
```
- **Результат**: Повертає JSON-дані.

---

### Маршрут для завантаження файлу
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

---

## Структура проекту

```
├── src/
│   ├── Core/                # Основні класи (Router, View, Container)
│   ├── Controllers/         # Контролери
│   ├── views/               # Шаблони та блоки
├── tests/                   # Тести
├── config/                  # Конфігурація (routes.yaml)
├── public/                  # Публічна директорія (index.php, .htaccess)
├── docker-compose.yml       # Docker конфігурація
├── composer.json            # Composer конфігурація
```

---

## Встановлення

### Через Docker
1. Запустіть Docker-контейнери:
   ```bash
   docker-compose up -d
   ```
2. Відкрийте проект у браузері за адресою: [http://localhost:8080](http://localhost:8080).

### Через Composer
1. Встановіть залежності:
   ```bash
   composer install
   ```
2. Запустіть локальний сервер:
   ```bash
   php -S localhost:8080 -t public
   ```

---

## Тестування
Запустіть тести через PHPUnit:
```bash
vendor/bin/phpunit
```

---

## Документація
Документація доступна у файлі `docs/index.html`. Для генерації документації використовуйте phpDocumentor:
```bash
docker-compose exec web vendor/bin/phpdoc
```

---

## Ліцензія
Проект розповсюджується під ліцензією MIT.
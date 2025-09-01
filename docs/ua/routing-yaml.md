
# Маршрутизація через YAML

## Конфігурація маршруту
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

## Базовий шлях для контролера
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

## Приклад YAML-файлу
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

## Як працює маршрутизація через YAML
1. **Явні маршрути**:
   - Визначаються в секції `routes`.
   - Кожен маршрут має `controller`, `action`, `method` і `path`.

2. **Базові шляхи контролерів**:
   - Визначаються в секції `controllers`.
   - Додають базовий шлях для всіх методів контролера.

3. **Динамічні параметри**:
   - Параметри в маршрутах, наприклад `{id}`, автоматично передаються в метод контролера.

---

## Приклад використання
### YAML-конфігурація:
```yaml
routes:
  - controller: Site\UserController
    action: getUser
    method: GET
    path: /user/{id}
```

### Контролер:
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

### Результат:
- **Шлях**: `/user/123`
- **Результат**: Повертає текст "User ID: 123".

---

Ця документація тепер охоплює всі аспекти маршрутизації через YAML, включаючи явні маршрути, базові шляхи контролерів і динамічні параметри.## Маршрутизація через YAML

## Конфігурація маршруту
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

## Базовий шлях для контролера
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
### YAML-конфігурація:
```yaml
routes:
  - controller: Site\UserController
    action: getUser
    method: GET
    path: /user/{id}
```

### Контролер:
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

### Результат:
- **Шлях**: `/user/123`
- **Результат**: Повертає текст "User ID: 123".

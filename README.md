# Leopard Skeleton

## Огляд
Leopard Skeleton — це базовий каркас для PHP-додатків, який підтримує MVC-архітектуру, роутинг, шаблони, блоки та тестування. Проект побудований на сучасних технологіях, таких як PHP 8.3, PSR-7, та Symfony YAML.

---

## Основні функції

### Роутинг
- Підтримка маршрутизації через атрибути `#[Route]` та конфігурацію YAML.
- Динамічні маршрути з параметрами, наприклад: `/user/{id}`.
- Підтримка HTTP-методів: `GET`, `POST`, `PUT`, `DELETE`, `PATCH`, `OPTIONS`, `HEAD`.

### Шаблони та блоки
- Гнучка система шаблонів із підтримкою макетів (`layouts`) та блоків (`blocks`).
- Можливість створення специфічних блоків для кожного макету.
- Рендеринг HTML через клас `View`.

### Контролери
- Контролери організовані за просторами імен (`Site`, `Admin`, `Api`).
- Базовий клас `HtmlController` для роботи з шаблонами.

### Тестування
- Тестування роутів через PHPUnit.
- Тести для контейнера залежностей.

### Docker
- Docker-контейнер для PHP 8.3 з Apache.
- MySQL для бази даних.
- phpMyAdmin для управління базою даних.

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

## Приклади використання

### Роутинг через атрибути
```php
#[Route('/user/{id}', method: 'GET')]
public function getUser(string $id): string
{
    return "User ID: $id";
}
```

### Рендеринг шаблонів
```php
return $this->view->render('site/home', [
    'title' => 'Home Page',
    'message' => 'Welcome to the Home Page!'
]);
```

### Використання блоків
```php
<?= $this->renderBlock('header', ['title' => 'Page Title']) ?>
```

---

## Встановлення

### Через Docker
1. Запустіть Docker-контейнери:
   ```bash
   ./manage-docker.sh start
   ```
2. Відкрийте проект у браузері за адресою: [http://localhost:8080](http://localhost:8080).

### Через Composer
1. Встановіть залежності:
   ```bash
   ./manage-composer.sh install
   ```
2. Запустіть локальний сервер:
   ```bash
   php -S localhost:8080 -t public
   ```

---

## Тестування
Запустіть тести через Docker:
```bash
./run-tests.sh
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
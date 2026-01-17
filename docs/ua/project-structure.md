# Структура проекту

```
├── src/
│   ├── Commands/            # Консольні команди
│   ├── Controllers/         # Контролери
│       ├── Site/            # Контролери розділу Site
|   ├── EventHendlers        # Обробники подій
│   ├── Models/              # Моделі
│   ├── Services/            # Сервіси
│   ├── views/               # Шаблони та блоки
│       ├── site/            # Файли розділу
│           ├── blocks/      # Блоки
│           ├── layouts/     # Шаблони
│               ├── main.php # Головний шаблон
│               ├── main/    # Блоки для шаблону main
├── storage/                 # Сховище
│   ├── database/            # Тека для sqlite бази даних, якщо використовується
│   ├── logs/                # Логи
│       ├── xdebug/          # Тека для логів xdebug
├── tests/                   # Тести
├── config/                  # Конфігурації
├── public/                  # Публічна директорія (index.php, .htaccess)
│   ├── assets/              # Публічні статичні файли
│       ├── css/             # Стилі
│       ├── js/              # Скрипти
├── docker-compose.yml       # Docker конфігурація
├── composer.json            # Composer конфігурація
```

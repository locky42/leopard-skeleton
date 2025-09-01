# Робота з параметрами

## Огляд
Клас `Params` дозволяє завантажувати параметри з PHP-файлів, отримувати значення за ключами, перевіряти їх існування, встановлювати нові значення та видаляти параметри.

## Основні методи

### Завантаження параметрів
```php
$params = new Params();
$params->load(__DIR__ . '/../config/params.php');
```
- **`load`**: Завантажує параметри з PHP-файлу, який повертає масив.

### Отримання значення за ключем
```php
$appName = $params->get('app.name', 'Default App');
```
- **`get`**: Повертає значення за ключем. Якщо ключ не знайдено, повертається значення за замовчуванням.

### Встановлення значення
```php
$params->set('app.debug', true);
```
- **`set`**: Встановлює значення за ключем.

### Перевірка існування ключа
```php
if ($params->has('app.version')) {
    echo $params->get('app.version');
}
```
- **`has`**: Перевіряє, чи існує ключ у параметрах.

---

## Приклад PHP-файлу з параметрами
```php
<?php

return [
    'app.name' => 'Leopard Skeleton',
    'app.version' => '1.0.0',
    'database.host' => 'localhost',
    'database.port' => 3306,
];
```

---

## Приклад використання в проекті
### Завантаження конфігурації та параметрів
```php
$config = new Config();
$config->load(__DIR__ . '/../config/app.yaml');

$params = new Params();
$params->load(__DIR__ . '/../config/params.php');
```

### Використання значень
```php
echo $config->get('database.host'); // Виведе 'localhost'
echo $params->get('app.name'); // Виведе 'Leopard Skeleton'
```

### Встановлення нових значень
```php
$config->set('app.debug', true);
$params->set('app.theme', 'dark');
```

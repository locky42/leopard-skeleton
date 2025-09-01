# Working with Parameters

## Overview
The `Params` class allows you to load parameters from PHP files, retrieve values by keys, check their existence, set new values, and delete parameters.

## Main Methods

### Loading Parameters
```php
$params = new Params();
$params->load(__DIR__ . '/../config/params.php');
```
- **`load`**: Loads parameters from a PHP file that returns an array.

### Retrieving a Value by Key
```php
$appName = $params->get('app.name', 'Default App');
```
- **`get`**: Returns the value by key. If the key is not found, the default value is returned.

### Setting a Value
```php
$params->set('app.debug', true);
```
- **`set`**: Sets a value by key.

### Checking Key Existence
```php
if ($params->has('app.version')) {
    echo $params->get('app.version');
}
```
- **`has`**: Checks if a key exists in the parameters.

---

## Example PHP File with Parameters
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

## Example Usage in a Project
### Loading Configuration and Parameters
```php
$config = new Config();
$config->load(__DIR__ . '/../config/app.yaml');

$params = new Params();
$params->load(__DIR__ . '/../config/params.php');
```

### Using Values
```php
echo $config->get('database.host'); // Outputs 'localhost'
echo $params->get('app.name'); // Outputs 'Leopard Skeleton'
```

### Setting New Values
```php
$config->set('app.debug', true);
$params->set('app.theme', 'dark');
```

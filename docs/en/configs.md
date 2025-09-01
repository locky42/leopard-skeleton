# Working with Configs

## Overview
The `Config` class allows you to load configuration data from YAML files, retrieve values by keys, check their existence, and set new values.

## Main Methods

### Loading Configuration
```php
$config = new Config();
$config->load(__DIR__ . '/../config/app.yaml');
```
- **`load`**: Loads configuration from a YAML file.

### Retrieving a Value by Key
```php
$dbHost = $config->get('database.host', 'localhost');
```
- **`get`**: Returns the value for a key. If the key is not found, the default value is returned.

### Setting a Value
```php
$config->set('app.debug', true);
```
- **`set`**: Sets a value for a key.

### Checking Key Existence
```php
if ($config->has('app.name')) {
    echo $config->get('app.name');
}
```
- **`has`**: Checks if a key exists in the configuration.

### Retrieving All Values
```php
$allConfig = $config->getAll();
```
- **`getAll`**: Returns the entire configuration array.

# Контейнер залежностей (Dependency Injection Container)

## Огляд
Контейнер (`Container`) — це клас для управління залежностями, який дозволяє реєструвати сервіси та отримувати їх екземпляри. Він реалізує принципи Dependency Injection (DI) для спрощення роботи з залежностями.

## Основні методи контейнера

### Реєстрація сервісу
```php
$container->set(Parsedown::class, function () {
    return new Parsedown();
});
```
- **`set`**: Реєструє сервіс у контейнері. Ви передаєте унікальний ідентифікатор (`Parsedown::class`) та функцію, яка створює екземпляр сервісу.

### Отримання сервісу
```php
$parsedown = $container->get(Parsedown::class);
echo $parsedown->text('Привіт, _Parsedown_!');
```
- **`get`**: Повертає екземпляр сервісу за його ідентифікатором. Якщо сервіс ще не створений, контейнер створює його за допомогою функції, переданої в `set`.

### Перевірка наявності сервісу
```php
if ($container->has(Parsedown::class)) {
    $parsedown = $container->get(Parsedown::class);
}
```
- **`has`**: Перевіряє, чи зареєстрований сервіс або чи існує клас із заданим ідентифікатором.

## Приклад використання в контролері

```php
namespace App\Controllers\Site;

class HomeController extends HtmlController
{
    public function index(): string
    {
        $parsedown = $this->get(Parsedown::class);
        $markdown = file_get_contents(__DIR__ . '/../../../README.md');
        $documentation = $parsedown->text($markdown);

        return $this->view->render('home', [
            'title' => 'Leopard Framework',
            'documentation' => $documentation
        ]);
    }
}
```

## Тестування контейнера
Контейнер протестований у файлі `tests/Core/ContainerTest.php`. Наприклад:

```php
public function testGetInstance(): void
{
    $container = new Container();
    $instance = $container->get(stdClass::class);

    $this->assertInstanceOf(stdClass::class, $instance);
}
```

## Переваги використання контейнера
- **Легка реєстрація сервісів**: Ви можете реєструвати будь-які класи або функції.
- **Lazy Loading**: Сервіси створюються лише тоді, коли вони потрібні.
- **Гнучкість**: Контейнер дозволяє легко управляти залежностями у вашому проекті.

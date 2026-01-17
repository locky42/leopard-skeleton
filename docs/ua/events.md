# Події (Events)

## Зміст

- [Вступ](#vstup)
- [Основні концепції](#osnovni-kontseptsii)
- [EventManager](#eventmanager)
- [Створення подій](#stvorennia-podii)
- [Реєстрація обробників](#reiestratsiia-obrobnykiv)
- [Виклик подій](#vyklyk-podii)
- [Stoppable Events](#stoppable-events)
- [Вбудовані події](#vbudovani-podii)
- [Приклади використання](#pryklady-vykorystannia)
- [Найкращі практики](#naikrashchi-praktyky)
- [Додаткові можливості](#dodatkovi-mozhlyvosti)
- [Відмінності від інших систем подій](#vidminnosti-vid-inshykh-system-podii)
- [Troubleshooting](#troubleshooting)
- [Підсумок](#pidsumok)

---

## Вступ

Leopard Events - це система керування подіями, яка реалізує стандарт PSR-14 (Event Dispatcher). Вона дозволяє створювати слабко зв'язані компоненти додатку, де різні частини системи можуть реагувати на події без прямої залежності один від одного.

### Переваги використання подій

- **Слабке зв'язування**: Компоненти не залежать безпосередньо один від одного
- **Розширюваність**: Легко додавати нову функціональність без зміни існуючого коду
- **Тестованість**: Можна тестувати компоненти ізольовано
- **Підтримка PSR-14**: Відповідає стандартам PHP-FIG

---

## Основні концепції

Система подій складається з трьох основних компонентів:

### 1. Event (Подія)

Об'єкт, який представляє подію в системі. Містить дані про те, що сталося.

### 2. Listener (Слухач)

Callable-функція або метод, який виконується при виникненні події.

### 3. Event Dispatcher (Диспетчер подій)

Компонент, який відповідає за доставку подій відповідним слухачам.

---

## EventManager

`EventManager` - це статичний клас, який надає зручний API для роботи з подіями.

### Основні методи

#### `addEvent()`

Реєструє слухача для певної події.

```php
EventManager::addEvent(string|object $event, callable $listener): void
```

**Параметри:**
- `$event` - Клас події (string) або об'єкт події
- `$listener` - Callable-функція обробник

**Приклад:**

```php
use Leopard\Events\EventManager;

EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    // Логіка обробки події
    echo "Новий користувач: " . $event->user->email;
});
```

#### `doEvent()`

Викликає подію та передає їй аргументи.

```php
EventManager::doEvent(string $event, ...$args): object
```

**Параметри:**
- `$event` - Клас події (string)
- `...$args` - Аргументи для конструктора події

**Повертає:** Об'єкт події після обробки

**Приклад:**

```php
$user = new User();
$event = EventManager::doEvent(UserRegistered::class, $user);
```

#### `removeEvent()`

Видаляє слухача події.

```php
EventManager::removeEvent(object $event, callable $listener): void
```

**Параметри:**
- `$event` - Об'єкт події
- `$listener` - Callable-функція, яку потрібно видалити

#### `getDispatcher()`

Повертає екземпляр EventDispatcher.

```php
EventManager::getDispatcher(): EventDispatcher
```

#### `getProvider()`

Повертає екземпляр ListenerProvider.

```php
EventManager::getProvider(): ListenerProvider
```

---

## Створення подій

### Проста подія

Створіть клас події у директорії `src/Events/`:

```php
<?php

namespace App\Events;

class UserRegistered
{
    public function __construct(
        public readonly User $user
    ) {}
}
```

### Подія з додатковими властивостями

```php
<?php

namespace App\Events;

class OrderCreated
{
    public function __construct(
        public readonly Order $order,
        public readonly User $customer
    ) {}
    
    public function getTotal(): float
    {
        return $this->order->total;
    }
}
```

### Подія, яку можна зупинити (Stoppable Event)

Для подій, які можуть бути зупинені під час обробки:

```php
<?php

namespace App\Events;

use Leopard\Events\Dispatcher\StoppableEvent;

class BeforeUserDelete extends StoppableEvent
{
    public function __construct(
        public readonly User $user,
        public bool $cancelled = false
    ) {}
}
```

---

## Реєстрація обробників

### Через EventHandler

Рекомендований спосіб - створення обробників у директорії `src/EventHandlers/`.

#### 1. Створіть клас обробника

```php
<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use App\Events\UserRegistered;

class UserEventHandler implements EventHandlerInterface
{
    public function boot()
    {
        // Реєстрація слухача для події реєстрації користувача
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $this->sendWelcomeEmail($event->user);
        });
        
        // Можна додати більше слухачів
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $this->createUserProfile($event->user);
        });
    }
    
    private function sendWelcomeEmail(User $user): void
    {
        // Логіка відправки email
    }
    
    private function createUserProfile(User $user): void
    {
        // Логіка створення профілю
    }
}
```

#### 2. Автоматичне завантаження

Всі обробники з директорії `src/EventHandlers/` автоматично завантажуються у `bootstrap.php`:

```php
foreach (glob(__DIR__ . '/src/EventHandlers/*.php') as $filename) {
    $className = 'App\EventHandlers\\' . basename($filename, '.php');
    if ($className instanceof \App\EventHandlers\EventHandlerInterface) {
        $handler = new $className();
        $handler->boot();
    }
}
```

### Інлайн-реєстрація

Можна реєструвати обробники безпосередньо у коді:

```php
use Leopard\Events\EventManager;
use App\Events\UserRegistered;

EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    // Ваша логіка
});
```

### Використання методів класу

```php
class UserService
{
    public function __construct()
    {
        EventManager::addEvent(UserRegistered::class, [$this, 'handleRegistration']);
    }
    
    public function handleRegistration(UserRegistered $event): void
    {
        // Обробка реєстрації
    }
}
```

### Використання статичних методів

```php
EventManager::addEvent(UserRegistered::class, [UserNotifier::class, 'notify']);
```

---

## Виклик подій

### Базовий виклик

```php
use Leopard\Events\EventManager;
use App\Events\UserRegistered;

$user = new User();
$user->email = 'user@example.com';

// Виклик події
EventManager::doEvent(UserRegistered::class, $user);
```

### Виклик з кількома аргументами

```php
use App\Events\OrderCreated;

$order = new Order();
$customer = new User();

EventManager::doEvent(OrderCreated::class, $order, $customer);
```

### Використання результату

```php
$event = EventManager::doEvent(UserRegistered::class, $user);

// Перевірка, чи була подія оброблена
if (isset($event->processed)) {
    echo "Подію оброблено!";
}
```

### Виклик без аргументів

```php
EventManager::doEvent(AppStarted::class);
```

---

## Stoppable Events

Stoppable Events дозволяють зупинити виконання наступних слухачів під час обробки події.

### Створення Stoppable Event

```php
<?php

namespace App\Events;

use Leopard\Events\Dispatcher\StoppableEvent;

class BeforeUserSave extends StoppableEvent
{
    public function __construct(
        public User $user,
        public array $errors = []
    ) {}
}
```

### Використання

```php
// Реєстрація слухачів
EventManager::addEvent(BeforeUserSave::class, function (BeforeUserSave $event) {
    if (empty($event->user->email)) {
        $event->errors[] = 'Email обов\'язковий';
        $event->stopPropagation(); // Зупиняємо подальшу обробку
    }
});

EventManager::addEvent(BeforeUserSave::class, function (BeforeUserSave $event) {
    // Цей слухач не виконається, якщо попередній зупинив обробку
    echo "Перевірка унікальності email...";
});

// Виклик події
$user = new User();
$event = EventManager::doEvent(BeforeUserSave::class, $user);

if ($event->isPropagationStopped()) {
    echo "Валідація не пройшла: " . implode(', ', $event->errors);
}
```

### Методи StoppableEvent

- `stopPropagation()`: void - Зупиняє обробку події
- `isPropagationStopped()`: bool - Перевіряє, чи зупинена обробка

---

## Вбудовані події

### AfterViewInit

Подія викликається після ініціалізації представлення (View).

**Клас:** `Leopard\Core\Events\AfterViewInit`

**Властивості:**
- `view`: ?View - Екземпляр View

**Приклад використання:**

```php
<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use Leopard\Core\Events\AfterViewInit;

class AfterViewInitHandler implements EventHandlerInterface
{
    public function boot()
    {
        EventManager::addEvent(AfterViewInit::class, function (AfterViewInit $e) {
            $view = $e->view;
            $seo = $view->getSeo();
            
            // Встановлення SEO параметрів
            $seo->setCharset('UTF-8');
            $seo->addMetaTag('viewport', 'width=device-width, initial-scale=1.0');
            $seo->addMetaTag('author', 'Your Company');
        });
    }
}
```

---

## Приклади використання

### Приклад 1: Система повідомлень

```php
<?php

namespace App\Events;

class UserRegistered
{
    public function __construct(public readonly User $user) {}
}
```

```php
<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use App\Events\UserRegistered;
use App\Services\EmailService;
use App\Services\LoggerService;

class NotificationHandler implements EventHandlerInterface
{
    public function boot()
    {
        // Відправка welcome email
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $emailService = new EmailService();
            $emailService->sendWelcome($event->user);
        });
        
        // Логування реєстрації
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $logger = new LoggerService();
            $logger->info("Новий користувач зареєстрований: " . $event->user->email);
        });
        
        // Створення початкових налаштувань
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $event->user->settings()->create([
                'theme' => 'light',
                'language' => 'uk',
                'notifications' => true
            ]);
        });
    }
}
```

### Приклад 2: Модифікація даних

```php
<?php

namespace App\Events;

class BeforeProductSave
{
    public function __construct(public Product $product) {}
}
```

```php
<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use App\Events\BeforeProductSave;

class ProductHandler implements EventHandlerInterface
{
    public function boot()
    {
        EventManager::addEvent(BeforeProductSave::class, function (BeforeProductSave $event) {
            $product = $event->product;
            
            // Автоматичне генерування slug
            if (empty($product->slug)) {
                $product->slug = $this->generateSlug($product->name);
            }
            
            // Обчислення знижки
            if ($product->sale_price) {
                $product->discount_percent = round(
                    (($product->price - $product->sale_price) / $product->price) * 100
                );
            }
        });
    }
    
    private function generateSlug(string $name): string
    {
        return strtolower(str_replace(' ', '-', $name));
    }
}
```

```php
// У контролері або сервісі
$product = new Product();
$product->name = 'Ноутбук ASUS';
$product->price = 25000;
$product->sale_price = 22000;

EventManager::doEvent(BeforeProductSave::class, $product);
$product->save();
```

### Приклад 3: Валідація з можливістю скасування

```php
<?php

namespace App\Events;

use Leopard\Events\Dispatcher\StoppableEvent;

class BeforeOrderProcess extends StoppableEvent
{
    public array $errors = [];
    
    public function __construct(
        public Order $order,
        public User $customer
    ) {}
    
    public function addError(string $error): void
    {
        $this->errors[] = $error;
        $this->stopPropagation();
    }
    
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
```

```php
<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use App\Events\BeforeOrderProcess;

class OrderValidationHandler implements EventHandlerInterface
{
    public function boot()
    {
        // Перевірка товарів
        EventManager::addEvent(BeforeOrderProcess::class, function (BeforeOrderProcess $event) {
            if ($event->order->items->isEmpty()) {
                $event->addError('Замовлення не може бути порожнім');
                return;
            }
        });
        
        // Перевірка наявності товарів
        EventManager::addEvent(BeforeOrderProcess::class, function (BeforeOrderProcess $event) {
            foreach ($event->order->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    $event->addError("Товар '{$item->product->name}' відсутній у потрібній кількості");
                    return;
                }
            }
        });
        
        // Перевірка адреси доставки
        EventManager::addEvent(BeforeOrderProcess::class, function (BeforeOrderProcess $event) {
            if (empty($event->order->shipping_address)) {
                $event->addError('Вкажіть адресу доставки');
                return;
            }
        });
    }
}
```

```php
// У сервісі обробки замовлень
public function processOrder(Order $order, User $customer): bool
{
    $event = EventManager::doEvent(BeforeOrderProcess::class, $order, $customer);
    
    if ($event->hasErrors()) {
        throw new ValidationException($event->errors);
    }
    
    // Продовжуємо обробку замовлення
    $order->status = 'processing';
    $order->save();
    
    return true;
}
```

### Приклад 4: Робота зі змінюваним об'єктом

```php
<?php

namespace App\Events;

class DataProcessing
{
    public array $data;
    
    public function __construct(array $initialData)
    {
        $this->data = $initialData;
    }
}
```

```php
EventManager::addEvent(DataProcessing::class, function (DataProcessing $event) {
    // Додавання timestamp
    $event->data['processed_at'] = time();
});

EventManager::addEvent(DataProcessing::class, function (DataProcessing $event) {
    // Фільтрація даних
    $event->data = array_filter($event->data, function($value) {
        return $value !== null;
    });
});

EventManager::addEvent(DataProcessing::class, function (DataProcessing $event) {
    // Додавання метаданих
    $event->data['meta'] = [
        'version' => '1.0',
        'source' => 'api'
    ];
});

// Виклик
$initialData = ['name' => 'Test', 'value' => null];
$event = EventManager::doEvent(DataProcessing::class, $initialData);
var_dump($event->data);
```

### Приклад 5: Множинні обробники з різною логікою

```php
<?php

namespace App\Events;

class UserLogin
{
    public function __construct(
        public readonly User $user,
        public readonly string $ipAddress
    ) {}
}
```

```php
<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use App\Events\UserLogin;

class SecurityHandler implements EventHandlerInterface
{
    public function boot()
    {
        // Логування входу
        EventManager::addEvent(UserLogin::class, function (UserLogin $event) {
            file_put_contents(
                'storage/logs/login.log',
                sprintf(
                    "[%s] User %s logged in from %s\n",
                    date('Y-m-d H:i:s'),
                    $event->user->email,
                    $event->ipAddress
                ),
                FILE_APPEND
            );
        });
        
        // Оновлення last_login
        EventManager::addEvent(UserLogin::class, function (UserLogin $event) {
            $event->user->last_login_at = new \DateTime();
            $event->user->last_login_ip = $event->ipAddress;
            $event->user->save();
        });
        
        // Перевірка безпеки
        EventManager::addEvent(UserLogin::class, function (UserLogin $event) {
            $suspiciousIPs = ['192.168.1.666', '10.0.0.666'];
            if (in_array($event->ipAddress, $suspiciousIPs)) {
                // Відправка сповіщення адміністратору
                $this->notifyAdmin($event->user, $event->ipAddress);
            }
        });
    }
}
```

---

## Найкращі практики

### 1. Іменування подій

Використовуйте дієслова у минулому часі для подій, що вже відбулися:
- ✅ `UserRegistered`, `OrderCreated`, `PaymentProcessed`
- ❌ `RegisterUser`, `CreateOrder`, `ProcessPayment`

Використовуйте `Before...` для подій, що відбуваються перед дією:
- ✅ `BeforeUserSave`, `BeforeOrderDelete`

### 2. Структура подій

```php
// ✅ Добре: незмінні дані
class UserRegistered
{
    public function __construct(
        public readonly User $user,
        public readonly \DateTime $registeredAt
    ) {}
}

// ❌ Погано: змінні дані без необхідності
class UserRegistered
{
    public User $user;
    public \DateTime $registeredAt;
}
```

### 3. Не покладайтеся на порядок виконання

Слухачі можуть виконуватися у будь-якому порядку. Не створюйте залежності між ними.

### 4. Уникайте важких операцій

Для важких операцій використовуйте черги:

```php
EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    // ❌ Погано: синхронна відправка email
    $emailService->send($event->user);
    
    // ✅ Добре: додавання у чергу
    Queue::push(new SendWelcomeEmail($event->user));
});
```

### 5. Обробка помилок

```php
EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    try {
        $this->sendEmail($event->user);
    } catch (\Exception $e) {
        // Логування помилки, але не кидання винятку
        Log::error('Failed to send welcome email: ' . $e->getMessage());
    }
});
```

### 6. Тестування

```php
use PHPUnit\Framework\TestCase;

class UserRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        EventManager::getProvider()->clearListeners();
    }
    
    public function testUserRegistrationEvent()
    {
        $handled = false;
        
        EventManager::addEvent(UserRegistered::class, function ($event) use (&$handled) {
            $handled = true;
        });
        
        $user = new User();
        EventManager::doEvent(UserRegistered::class, $user);
        
        $this->assertTrue($handled);
    }
}
```

---

## Додаткові можливості

### Очищення слухачів

```php
// Очистити всіх слухачів
EventManager::getProvider()->clearListeners();
```

### Отримання всіх слухачів

```php
$listeners = EventManager::getProvider()->getListeners();
```

### Отримання слухачів для конкретної події

```php
$listeners = EventManager::getProvider()->getListener(UserRegistered::class);
```

---

## Відмінності від інших систем подій

### PSR-14 Compliance

Leopard Events повністю відповідає стандарту PSR-14, що означає:
- Сумісність з іншими PSR-14 бібліотеками
- Можливість заміни компонентів
- Стандартизований API

### Порівняння з Laravel Events

| Особливість | Leopard Events | Laravel Events |
|------------|---------------|----------------|
| PSR-14 | ✅ Так | ❌ Ні |
| Stoppable Events | ✅ Так | ✅ Так |
| Черги | ❌ Інтеграція потрібна | ✅ Вбудовано |
| Event Discovery | ❌ Ні | ✅ Так |
| Простота | ✅ Мінімалістичний | ⚠️ Багатофункціональний |

---

## Troubleshooting

### Подія не викликається

1. Перевірте, чи зареєстровано обробник:
```php
$listeners = EventManager::getProvider()->getListener(YourEvent::class);
var_dump($listeners);
```

2. Перевірте, чи завантажується EventHandler у `bootstrap.php`

3. Переконайтеся, що клас події існує та має правильний namespace

### Обробник викликається кілька разів

Можливо, ви реєструєте обробник декілька разів. Використовуйте `removeEvent()` або `clearListeners()`.

### Stoppable Event не зупиняється

Переконайтеся, що:
1. Клас події наслідує `StoppableEvent`
2. Викликається метод `stopPropagation()`
3. Перевірка `isPropagationStopped()` здійснюється у правильному місці

---

## Підсумок

Система подій Leopard Events надає:

- ✅ PSR-14 сумісність
- ✅ Простий та зрозумілий API
- ✅ Підтримку Stoppable Events
- ✅ Гнучку реєстрацію обробників
- ✅ Легке тестування
- ✅ Мінімальні залежності

Використовуйте події для створення слабко зв'язаних, розширюваних додатків з чистою архітектурою.

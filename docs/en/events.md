# Events

## Table of Contents

- [Introduction](#introduction)
- [Core Concepts](#core-concepts)
- [EventManager](#eventmanager)
- [Creating Events](#creating-events)
- [Registering Handlers](#registering-handlers)
- [Dispatching Events](#dispatching-events)
- [Stoppable Events](#stoppable-events)
- [Built-in Events](#built-in-events)
- [Usage Examples](#usage-examples)
- [Best Practices](#best-practices)
- [Additional Features](#additional-features)
- [Differences from Other Event Systems](#differences-from-other-event-systems)
- [Troubleshooting](#troubleshooting)
- [Summary](#summary)

---

## Introduction

Leopard Events is an event management system that implements the PSR-14 (Event Dispatcher) standard. It enables creating loosely coupled application components, where different parts of the system can respond to events without direct dependencies on each other.

### Benefits of Using Events

- **Loose Coupling**: Components don't depend directly on each other
- **Extensibility**: Easy to add new functionality without modifying existing code
- **Testability**: Components can be tested in isolation
- **PSR-14 Support**: Complies with PHP-FIG standards

---

## Core Concepts

The event system consists of three main components:

### 1. Event

An object that represents an event in the system. Contains data about what happened.

### 2. Listener

A callable function or method that executes when an event occurs.

### 3. Event Dispatcher

A component responsible for delivering events to their corresponding listeners.

---

## EventManager

`EventManager` is a static class that provides a convenient API for working with events.

### Core Methods

#### `addEvent()`

Registers a listener for a specific event.

```php
EventManager::addEvent(string|object $event, callable $listener): void
```

**Parameters:**
- `$event` - Event class (string) or event object
- `$listener` - Callable handler function

**Example:**

```php
use Leopard\Events\EventManager;

EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    // Event handling logic
    echo "New user: " . $event->user->email;
});
```

#### `doEvent()`

Dispatches an event and passes arguments to it.

```php
EventManager::doEvent(string $event, ...$args): object
```

**Parameters:**
- `$event` - Event class (string)
- `...$args` - Arguments for the event constructor

**Returns:** Event object after processing

**Example:**

```php
$user = new User();
$event = EventManager::doEvent(UserRegistered::class, $user);
```

#### `removeEvent()`

Removes an event listener.

```php
EventManager::removeEvent(object $event, callable $listener): void
```

**Parameters:**
- `$event` - Event object
- `$listener` - Callable function to remove

#### `getDispatcher()`

Returns the EventDispatcher instance.

```php
EventManager::getDispatcher(): EventDispatcher
```

#### `getProvider()`

Returns the ListenerProvider instance.

```php
EventManager::getProvider(): ListenerProvider
```

---

## Creating Events

### Simple Event

Create an event class in the `src/Events/` directory:

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

### Event with Additional Properties

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

### Stoppable Event

For events that can be stopped during processing:

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

## Registering Handlers

### Via EventHandler

The recommended approach is to create handlers in the `src/EventHandlers/` directory.

#### 1. Create a Handler Class

```php
<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use App\Events\UserRegistered;

class UserEventHandler implements EventHandlerInterface
{
    public function boot()
    {
        // Register listener for user registration event
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $this->sendWelcomeEmail($event->user);
        });
        
        // Can add more listeners
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $this->createUserProfile($event->user);
        });
    }
    
    private function sendWelcomeEmail(User $user): void
    {
        // Email sending logic
    }
    
    private function createUserProfile(User $user): void
    {
        // Profile creation logic
    }
}
```

#### 2. Automatic Loading

All handlers from the `src/EventHandlers/` directory are automatically loaded in `bootstrap.php`:

```php
foreach (glob(__DIR__ . '/src/EventHandlers/*.php') as $filename) {
    $className = 'App\EventHandlers\\' . basename($filename, '.php');
    if ($className instanceof \App\EventHandlers\EventHandlerInterface) {
        $handler = new $className();
        $handler->boot();
    }
}
```

### Inline Registration

You can register handlers directly in code:

```php
use Leopard\Events\EventManager;
use App\Events\UserRegistered;

EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    // Your logic
});
```

### Using Class Methods

```php
class UserService
{
    public function __construct()
    {
        EventManager::addEvent(UserRegistered::class, [$this, 'handleRegistration']);
    }
    
    public function handleRegistration(UserRegistered $event): void
    {
        // Handle registration
    }
}
```

### Using Static Methods

```php
EventManager::addEvent(UserRegistered::class, [UserNotifier::class, 'notify']);
```

---

## Dispatching Events

### Basic Dispatch

```php
use Leopard\Events\EventManager;
use App\Events\UserRegistered;

$user = new User();
$user->email = 'user@example.com';

// Dispatch event
EventManager::doEvent(UserRegistered::class, $user);
```

### Dispatch with Multiple Arguments

```php
use App\Events\OrderCreated;

$order = new Order();
$customer = new User();

EventManager::doEvent(OrderCreated::class, $order, $customer);
```

### Using the Result

```php
$event = EventManager::doEvent(UserRegistered::class, $user);

// Check if event was processed
if (isset($event->processed)) {
    echo "Event processed!";
}
```

### Dispatch Without Arguments

```php
EventManager::doEvent(AppStarted::class);
```

---

## Stoppable Events

Stoppable Events allow you to stop execution of subsequent listeners during event processing.

### Creating a Stoppable Event

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

### Usage

```php
// Register listeners
EventManager::addEvent(BeforeUserSave::class, function (BeforeUserSave $event) {
    if (empty($event->user->email)) {
        $event->errors[] = 'Email is required';
        $event->stopPropagation(); // Stop further processing
    }
});

EventManager::addEvent(BeforeUserSave::class, function (BeforeUserSave $event) {
    // This listener won't execute if the previous one stopped processing
    echo "Checking email uniqueness...";
});

// Dispatch event
$user = new User();
$event = EventManager::doEvent(BeforeUserSave::class, $user);

if ($event->isPropagationStopped()) {
    echo "Validation failed: " . implode(', ', $event->errors);
}
```

### StoppableEvent Methods

- `stopPropagation()`: void - Stops event processing
- `isPropagationStopped()`: bool - Checks if processing is stopped

---

## Built-in Events

### AfterViewInit

Event dispatched after view (View) initialization.

**Class:** `Leopard\Core\Events\AfterViewInit`

**Properties:**
- `view`: ?View - View instance

**Usage Example:**

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
            
            // Set SEO parameters
            $seo->setCharset('UTF-8');
            $seo->addMetaTag('viewport', 'width=device-width, initial-scale=1.0');
            $seo->addMetaTag('author', 'Your Company');
        });
    }
}
```

---

## Usage Examples

### Example 1: Notification System

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
        // Send welcome email
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $emailService = new EmailService();
            $emailService->sendWelcome($event->user);
        });
        
        // Log registration
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $logger = new LoggerService();
            $logger->info("New user registered: " . $event->user->email);
        });
        
        // Create initial settings
        EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
            $event->user->settings()->create([
                'theme' => 'light',
                'language' => 'en',
                'notifications' => true
            ]);
        });
    }
}
```

### Example 2: Data Modification

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
            
            // Auto-generate slug
            if (empty($product->slug)) {
                $product->slug = $this->generateSlug($product->name);
            }
            
            // Calculate discount
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
// In controller or service
$product = new Product();
$product->name = 'ASUS Laptop';
$product->price = 25000;
$product->sale_price = 22000;

EventManager::doEvent(BeforeProductSave::class, $product);
$product->save();
```

### Example 3: Validation with Cancellation Support

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
        // Check items
        EventManager::addEvent(BeforeOrderProcess::class, function (BeforeOrderProcess $event) {
            if ($event->order->items->isEmpty()) {
                $event->addError('Order cannot be empty');
                return;
            }
        });
        
        // Check product availability
        EventManager::addEvent(BeforeOrderProcess::class, function (BeforeOrderProcess $event) {
            foreach ($event->order->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    $event->addError("Product '{$item->product->name}' is not available in the required quantity");
                    return;
                }
            }
        });
        
        // Check shipping address
        EventManager::addEvent(BeforeOrderProcess::class, function (BeforeOrderProcess $event) {
            if (empty($event->order->shipping_address)) {
                $event->addError('Please specify shipping address');
                return;
            }
        });
    }
}
```

```php
// In order processing service
public function processOrder(Order $order, User $customer): bool
{
    $event = EventManager::doEvent(BeforeOrderProcess::class, $order, $customer);
    
    if ($event->hasErrors()) {
        throw new ValidationException($event->errors);
    }
    
    // Continue processing order
    $order->status = 'processing';
    $order->save();
    
    return true;
}
```

### Example 4: Working with Mutable Object

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
    // Add timestamp
    $event->data['processed_at'] = time();
});

EventManager::addEvent(DataProcessing::class, function (DataProcessing $event) {
    // Filter data
    $event->data = array_filter($event->data, function($value) {
        return $value !== null;
    });
});

EventManager::addEvent(DataProcessing::class, function (DataProcessing $event) {
    // Add metadata
    $event->data['meta'] = [
        'version' => '1.0',
        'source' => 'api'
    ];
});

// Call
$initialData = ['name' => 'Test', 'value' => null];
$event = EventManager::doEvent(DataProcessing::class, $initialData);
var_dump($event->data);
```

### Example 5: Multiple Handlers with Different Logic

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
        // Log login
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
        
        // Update last_login
        EventManager::addEvent(UserLogin::class, function (UserLogin $event) {
            $event->user->last_login_at = new \DateTime();
            $event->user->last_login_ip = $event->ipAddress;
            $event->user->save();
        });
        
        // Security check
        EventManager::addEvent(UserLogin::class, function (UserLogin $event) {
            $suspiciousIPs = ['192.168.1.666', '10.0.0.666'];
            if (in_array($event->ipAddress, $suspiciousIPs)) {
                // Send notification to admin
                $this->notifyAdmin($event->user, $event->ipAddress);
            }
        });
    }
}
```

---

## Best Practices

### 1. Event Naming

Use past tense verbs for events that have already occurred:
- ✅ `UserRegistered`, `OrderCreated`, `PaymentProcessed`
- ❌ `RegisterUser`, `CreateOrder`, `ProcessPayment`

Use `Before...` for events that occur before an action:
- ✅ `BeforeUserSave`, `BeforeOrderDelete`

### 2. Event Structure

```php
// ✅ Good: immutable data
class UserRegistered
{
    public function __construct(
        public readonly User $user,
        public readonly \DateTime $registeredAt
    ) {}
}

// ❌ Bad: mutable data without necessity
class UserRegistered
{
    public User $user;
    public \DateTime $registeredAt;
}
```

### 3. Don't Rely on Execution Order

Listeners can execute in any order. Don't create dependencies between them.

### 4. Avoid Heavy Operations

For heavy operations, use queues:

```php
EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    // ❌ Bad: synchronous email sending
    $emailService->send($event->user);
    
    // ✅ Good: add to queue
    Queue::push(new SendWelcomeEmail($event->user));
});
```

### 5. Error Handling

```php
EventManager::addEvent(UserRegistered::class, function (UserRegistered $event) {
    try {
        $this->sendEmail($event->user);
    } catch (\Exception $e) {
        // Log error but don't throw exception
        Log::error('Failed to send welcome email: ' . $e->getMessage());
    }
});
```

### 6. Testing

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

## Additional Features

### Clearing Listeners

```php
// Clear all listeners
EventManager::getProvider()->clearListeners();
```

### Get All Listeners

```php
$listeners = EventManager::getProvider()->getListeners();
```

### Get Listeners for Specific Event

```php
$listeners = EventManager::getProvider()->getListener(UserRegistered::class);
```

---

## Differences from Other Event Systems

### PSR-14 Compliance

Leopard Events is fully compliant with the PSR-14 standard, which means:
- Compatibility with other PSR-14 libraries
- Ability to replace components
- Standardized API

### Comparison with Laravel Events

| Feature | Leopard Events | Laravel Events |
|---------|---------------|----------------|
| PSR-14 | ✅ Yes | ❌ No |
| Stoppable Events | ✅ Yes | ✅ Yes |
| Queues | ❌ Integration needed | ✅ Built-in |
| Event Discovery | ❌ No | ✅ Yes |
| Simplicity | ✅ Minimalistic | ⚠️ Feature-rich |

---

## Troubleshooting

### Event Not Triggered

1. Check if handler is registered:
```php
$listeners = EventManager::getProvider()->getListener(YourEvent::class);
var_dump($listeners);
```

2. Check if EventHandler is loaded in `bootstrap.php`

3. Make sure the event class exists and has the correct namespace

### Handler Called Multiple Times

You may be registering the handler multiple times. Use `removeEvent()` or `clearListeners()`.

### Stoppable Event Doesn't Stop

Make sure that:
1. Event class extends `StoppableEvent`
2. `stopPropagation()` method is called
3. `isPropagationStopped()` check is performed in the correct place

---

## Summary

The Leopard Events system provides:

- ✅ PSR-14 compatibility
- ✅ Simple and clear API
- ✅ Stoppable Events support
- ✅ Flexible handler registration
- ✅ Easy testing
- ✅ Minimal dependencies

Use events to create loosely coupled, extensible applications with clean architecture.

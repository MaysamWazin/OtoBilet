# OOP Refactored Backend - Design Patterns Implementation

## 📁 Klasör Yapısı

```
oop/
├── Database/
│   ├── SingletonDatabase.php      # Singleton Pattern
│   └── DatabaseAdapter.php        # Adapter Pattern
├── Models/
│   ├── User.php                   # User Entity
│   ├── Trip.php                   # Trip Entity
│   └── Ticket.php                 # Ticket Entity (State Pattern)
├── Repositories/
│   ├── RepositoryInterface.php    # Repository Interface
│   ├── UserRepository.php         # User Repository
│   ├── TripRepository.php         # Trip Repository
│   ├── TicketRepository.php       # Ticket Repository
│   ├── CompanyRepository.php     # Company Repository
│   ├── CouponRepository.php       # Coupon Repository
│   └── RepositoryFactory.php       # Abstract Factory Pattern
├── States/
│   └── TicketState.php            # State Pattern
├── Strategies/
│   └── PaymentStrategy.php        # Strategy Pattern
├── Proxies/
│   └── AuthProxy.php              # Proxy Pattern
├── Observers/
│   └── Observer.php               # Observer Pattern
├── Mediators/
│   └── Mediator.php               # Mediator Pattern
├── Facades/
│   └── BookingFacade.php          # Facade Pattern
├── Composite/
│   └── MenuComponent.php          # Composite Pattern
├── Visitors/
│   └── Visitor.php                # Visitor Pattern
└── autoload.php                    # PSR-4 Autoloader
```

## 🎨 Uygulanan Design Patterns

### 1. **Singleton Pattern**
- **Dosya**: `Database/SingletonDatabase.php`
- **Amaç**: Tek bir veritabanı bağlantı instance'ı sağlar
- **Kullanım**:
```php
$db = SingletonDatabase::getInstance();
$connection = $db->getConnection();
```

### 2. **Adapter Pattern**
- **Dosya**: `Database/DatabaseAdapter.php`
- **Amaç**: Farklı veritabanı türlerine uyum sağlar
- **Kullanım**:
```php
$adapter = new DatabaseAdapter();
$result = $adapter->query("SELECT * FROM User WHERE id = ?", [$id]);
```

### 3. **Abstract Factory Pattern**
- **Dosya**: `Repositories/RepositoryFactory.php`
- **Amaç**: Farklı repository türlerini oluşturur
- **Kullanım**:
```php
$factory = new RepositoryFactory($adapter);
$userRepo = $factory->createUserRepository();
```

### 4. **State Pattern**
- **Dosya**: `States/TicketState.php`
- **Amaç**: Ticket durumlarını yönetir (active, cancelled, expired)
- **Kullanım**:
```php
$ticket->cancel(); // State değişir
```

### 5. **Strategy Pattern**
- **Dosya**: `Strategies/PaymentStrategy.php`
- **Amaç**: Farklı ödeme stratejileri (Balance, Credit Card)
- **Kullanım**:
```php
$strategy = new BalancePaymentStrategy();
$strategy->pay($user, $amount);
```

### 6. **Proxy Pattern**
- **Dosya**: `Proxies/AuthProxy.php`
- **Amaç**: Authentication ve authorization kontrolü
- **Kullanım**:
```php
$proxy = new AuthProxy($userRepository);
$proxy->login($email, $password);
```

### 7. **Observer Pattern**
- **Dosya**: `Observers/Observer.php`
- **Amaç**: Event-driven architecture
- **Kullanım**:
```php
$eventManager->attach(new EmailNotificationObserver());
$eventManager->notify('ticket_purchased', $data);
```

### 8. **Mediator Pattern**
- **Dosya**: `Mediators/Mediator.php`
- **Amaç**: Karmaşık işlemleri koordine eder
- **Kullanım**:
```php
$mediator->purchaseTicket($user, $trip, $seats);
```

### 9. **Facade Pattern**
- **Dosya**: `Facades/BookingFacade.php`
- **Amaç**: Karmaşık işlemleri basitleştirir
- **Kullanım**:
```php
$facade = new BookingFacade();
$result = $facade->purchaseTicket($userId, $tripId, $seats);
```

### 10. **Composite Pattern**
- **Dosya**: `Composite/MenuComponent.php`
- **Amaç**: Menü yapısını oluşturur
- **Kullanım**:
```php
$menu = MenuBuilder::buildUserMenu();
echo $menu->render();
```

### 11. **Visitor Pattern**
- **Dosya**: `Visitors/Visitor.php`
- **Amaç**: Farklı rapor formatları oluşturur
- **Kullanım**:
```php
$visitor = new PDFReportVisitor();
$report = $ticketElement->accept($visitor);
```

## 🚀 Kullanım Örneği

```php
<?php
require_once __DIR__ . '/autoload.php';

use App\Database\DatabaseAdapter;
use App\Repositories\RepositoryFactory;
use App\Proxies\AuthProxy;
use App\Facades\BookingFacade;

// Autoloader
require_once __DIR__ . '/autoload.php';

// Database adapter
$adapter = new DatabaseAdapter();

// Repository factory
$factory = new RepositoryFactory($adapter);
$userRepository = $factory->createUserRepository();

// Authentication proxy
$authProxy = new AuthProxy($userRepository);
$authProxy->login('user@example.com', 'password');

// Booking facade
$bookingFacade = new BookingFacade();
$result = $bookingFacade->purchaseTicket(
    $userId,
    $tripId,
    [1, 2, 3],
    'DISCOUNT10'
);
```

## 📝 Notlar

- Tüm sınıflar PSR-4 namespace standardını kullanır
- Type hints kullanılmıştır
- Dependency Injection prensipleri uygulanmıştır
- SOLID prensipleri gözetilmiştir


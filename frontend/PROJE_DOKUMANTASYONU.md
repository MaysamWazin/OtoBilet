# Otobüs Bileti Satın Alma Platformu - Proje Dokümantasyonu

## 📋 İçindekiler
1. [Proje Genel Bakış](#proje-genel-bakış)
2. [Mimari Yapı](#mimari-yapı)
3. [Tasarım Modelleri (Design Patterns)](#tasarım-modelleri-design-patterns)
4. [Yazılım Geliştirme Metodolojileri](#yazılım-geliştirme-metodolojileri)
5. [Veritabanı Tasarımı ve Contracts](#veritabanı-tasarımı-ve-contracts)
6. [Güvenlik Yaklaşımları](#güvenlik-yaklaşımları)
7. [Teknoloji Stack](#teknoloji-stack)
8. [Deployment ve DevOps](#deployment-ve-devops)

---

## 🎯 Proje Genel Bakış

Bu proje, **çok kullanıcılı (multi-user)**, **rol tabanlı (role-based)** bir otobüs bileti satın alma platformudur. Sistem, üç farklı kullanıcı rolü ile çalışır:
- **User (Kullanıcı)**: Bilet satın alabilir, iptal edebilir
- **Company (Firma)**: Kendi seferlerini yönetebilir
- **Admin (Yönetici)**: Sistem genelinde tam yetki

### Temel Özellikler
- ✅ Kullanıcı kayıt/giriş sistemi
- ✅ Rol tabanlı erişim kontrolü (RBAC)
- ✅ Dinamik sefer arama ve listeleme
- ✅ Koltuk seçimi ve rezervasyon
- ✅ İndirim kuponu sistemi
- ✅ Bakiye tabanlı ödeme
- ✅ Bilet iptal ve iade
- ✅ PDF bilet oluşturma
- ✅ CRUD operasyonları (Firma, Sefer, Kullanıcı yönetimi)

---

## 🏗️ Mimari Yapı

### 1. **3-Tier Architecture (Üç Katmanlı Mimari)**

Proje, klasik üç katmanlı mimari yapısını kullanır:

```
┌─────────────────────────────────────┐
│   Presentation Layer (Frontend)    │
│   - HTML/CSS/JavaScript            │
│   - Nginx Web Server               │
└─────────────────────────────────────┘
              ↕
┌─────────────────────────────────────┐
│   Business Logic Layer (Backend)    │
│   - PHP Scripts                    │
│   - Session Management              │
│   - Authentication & Authorization  │
└─────────────────────────────────────┘
              ↕
┌─────────────────────────────────────┐
│   Data Access Layer (Database)      │
│   - SQLite Database                 │
│   - PDO (PHP Data Objects)          │
└─────────────────────────────────────┘
```

#### **Presentation Layer (Sunum Katmanı)**
- **Frontend**: Statik HTML sayfaları (modern CSS, animasyonlar)
- **Web Server**: Nginx (reverse proxy)
- **Responsive Design**: Mobile-first yaklaşım

#### **Business Logic Layer (İş Mantığı Katmanı)**
- **PHP Scripts**: İş mantığı ve veri işleme
- **Session Management**: Kullanıcı oturum yönetimi
- **Authentication**: Kimlik doğrulama
- **Authorization**: Yetkilendirme (rol kontrolü)

#### **Data Access Layer (Veri Erişim Katmanı)**
- **SQLite**: Dosya tabanlı veritabanı
- **PDO**: Prepared statements ile güvenli sorgular
- **Foreign Keys**: İlişkisel bütünlük

---

### 2. **Separation of Concerns (SoC) - Endişelerin Ayrılması**

Proje, farklı sorumlulukları ayrı dosyalara böler:

```
backend/src/
├── db.php              # Veritabanı bağlantısı (Single Responsibility)
├── auth_check.php      # Kimlik doğrulama mantığı
├── header.php          # Ortak HTML header
├── footer.php          # Ortak HTML footer
├── handle_*.php        # İş mantığı (Controller pattern)
└── *.php               # View dosyaları
```

**Avantajları:**
- ✅ Kod tekrarını azaltır
- ✅ Bakımı kolaylaştırır
- ✅ Test edilebilirlik artar
- ✅ Modüler yapı

---

## 🎨 Tasarım Modelleri (Design Patterns)

> **Not**: Bu proje **procedural PHP** kullanır. OOP (Object-Oriented Programming) class'ları yoktur. Ancak bazı tasarım desenlerinin **prensipleri** ve **yaklaşımları** kullanılmıştır.

### 1. **MVC (Model-View-Controller) Pattern - Kısmi Uygulama**

Proje, MVC pattern'inin **temel prensiplerini** kullanır, ancak tam bir MVC framework değildir:

```
Model (Veri Katmanı):
├── db.php              # Veritabanı bağlantısı
└── Veritabanı tabloları (User, Trips, Tickets, vb.)

View (Görünüm Katmanı):
├── *.html              # Frontend sayfaları (index.html, login.html, register.html)
├── header.php          # Ortak görünüm bileşenleri
└── footer.php          # Ortak görünüm bileşenleri

Controller (Kontrol Katmanı):
├── handle_login.php    # Giriş işlemleri
├── handle_register.php # Kayıt işlemleri
├── handle_purchase.php # Satın alma işlemleri
└── handle_add_trip.php # Sefer ekleme işlemleri
```

**Gerçek Kod Örneği:**
```php
// handle_login.php (Controller)
session_start();
$email = $_POST['email'];
$password = $_POST['password'];

require_once 'db.php';  // Model bağlantısı
$stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// View'a yönlendirme
header("Location: /index.php");
```

**Not**: Tam MVC değil, ancak katmanlar ayrılmış durumda.

---

### 2. **Template Method Pattern - Uygulanmış**

`header.php` ve `footer.php` dosyaları, template method pattern prensibini kullanır:

```php
// header.php - Ortak şablon (Gerçek kod)
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bilet Platformu</title>
    <style>/* Ortak CSS */</style>
</head>
<body>
    <header>/* Ortak header */</header>
    <main>

// Her sayfa şu şekilde kullanır:
require_once 'header.php';
// Sayfa içeriği
require_once 'footer.php';
```

**Avantajları:**
- ✅ Kod tekrarını önler
- ✅ Tutarlı görünüm sağlar
- ✅ Değişiklikleri tek yerden yapma

**Gerçek Kullanım**: `trips.php`, `trip_details.php`, `company_panel.php` gibi tüm sayfalarda kullanılıyor.

---

### 3. **Repository Pattern - Kısmi/Benzeri Yaklaşım**

Veritabanı işlemleri, repository pattern'in **benzeri** bir yapıda organize edilmiştir:

```php
// db.php - Merkezi veritabanı bağlantısı (Gerçek kod)
$db_path = __DIR__ . '/database/bilet_sistemi.sqlite';
$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON;');

// Her dosya db.php'yi require eder
require_once 'db.php';
$stmt = $pdo->prepare("SELECT ...");
```

**Not**: Gerçek bir Repository class'ı yok, ancak merkezi veritabanı bağlantısı var.

**Özellikler:**
- ✅ Tek bir veritabanı bağlantı noktası
- ✅ Prepared statements ile güvenlik
- ✅ Transaction desteği (handle_purchase.php'de kullanılıyor)

---

### 4. **Singleton Pattern - Benzeri Yaklaşım (Gerçek Singleton Değil)**

`db.php` dosyası, singleton pattern'in **benzeri** bir yaklaşım kullanır:

```php
// db.php (Gerçek kod)
$db_path = __DIR__ . '/database/bilet_sistemi.sqlite';
$pdo = new PDO('sqlite:' . $db_path);
```

**Not**: Gerçek singleton pattern değil (class yok), ancak her `require_once` çağrısında aynı `$pdo` değişkeni kullanılıyor.

**Avantajları:**
- ✅ Tek bir veritabanı bağlantı noktası
- ✅ Kod organizasyonu

**Sınırlamalar**: Her dosya `require_once 'db.php'` çağırdığında, aynı `$pdo` instance'ı kullanılır (PHP'nin include mekanizması sayesinde).

---

### 5. **Strategy Pattern - Uygulanmış (Rol Tabanlı Erişim)**

`auth_check.php` dosyası, farklı roller için farklı stratejiler uygular:

```php
// auth_check.php (Gerçek kod)
function require_role($required_roles) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit();
    }
    
    if (!is_array($required_roles)) {
        $required_roles = [$required_roles];
    }
    
    if (!in_array($_SESSION['role'], $required_roles)) {
        header('Location: /index.php');
        exit();
    }
}

// Gerçek kullanım örnekleri:
require_role('company');  // company_panel.php'de
require_role('admin');    // admin_panel.php'de
require_role('user');     // handle_purchase.php'de
```

**Stratejiler:**
- **User Strategy**: Bilet satın alma, iptal (`handle_purchase.php`, `cancel_ticket.php`)
- **Company Strategy**: Sefer yönetimi (`company_panel.php`, `add_trip.php`)
- **Admin Strategy**: Sistem yönetimi (`admin_panel.php`)

**Gerçek Kullanım**: Projede aktif olarak kullanılıyor.

---

### 6. **Facade Pattern - Kısmi Uygulama**

`header.php` dosyası, karmaşık işlemleri basitleştirir:

```php
// header.php (Gerçek kod)
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Oturum yönetimi
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bilet Platformu</title>
    <style>/* Ortak CSS */</style>
</head>
<body>
    <header>
        <!-- Navigation logic -->
        <!-- Rol bazlı menü gösterimi -->
    </header>
    <main>
```

**Avantajları:**
- ✅ Karmaşık işlemleri (session, HTML, CSS) tek dosyada toplar
- ✅ Basit arayüz sunar (`require_once 'header.php'`)
- ✅ Kod organizasyonu

**Not**: Gerçek bir Facade class'ı yok, ancak aynı prensip uygulanmış.

---

## 🔄 Yazılım Geliştirme Metodolojileri

### 1. **Procedural Programming (Yordamsal Programlama)**

Proje, **procedural programming** metodolojisini kullanır:

**Özellikler:**
- ✅ Fonksiyon tabanlı yapı
- ✅ Dosya bazlı organizasyon
- ✅ `require_once` ile modülerlik

**Örnek:**
```php
// auth_check.php
function require_role($required_roles) {
    // Fonksiyon tanımı
}

// handle_login.php
require_once 'auth_check.php';
require_role('user');
```

---

### 2. **Modular Programming (Modüler Programlama)**

Proje, modüler yapıda organize edilmiştir:

```
backend/src/
├── Core Modules (Çekirdek Modüller)
│   ├── db.php
│   ├── auth_check.php
│   ├── header.php
│   └── footer.php
│
├── Feature Modules (Özellik Modülleri)
│   ├── Authentication (Kimlik Doğrulama)
│   │   ├── handle_login.php
│   │   └── handle_register.php
│   ├── Trip Management (Sefer Yönetimi)
│   │   ├── trips.php
│   │   ├── trip_details.php
│   │   └── handle_add_trip.php
│   └── Ticket Management (Bilet Yönetimi)
│       ├── handle_purchase.php
│       └── cancel_ticket.php
```

**Avantajları:**
- ✅ Kod organizasyonu
- ✅ Bakım kolaylığı
- ✅ Yeniden kullanılabilirlik

---

### 3. **Separation of Concerns (SoC)**

Her dosya, tek bir sorumluluğa sahiptir:

| Dosya | Sorumluluk |
|-------|-----------|
| `db.php` | Veritabanı bağlantısı |
| `auth_check.php` | Kimlik doğrulama |
| `handle_login.php` | Giriş iş mantığı |
| `header.php` | HTML header render |
| `trips.php` | Sefer listeleme |

---

### 4. **DRY (Don't Repeat Yourself) Prensibi**

Ortak kodlar, tekrar kullanılabilir dosyalarda toplanmıştır:

```php
// Her sayfada:
require_once 'header.php';  // Tek bir header dosyası
require_once 'db.php';      // Tek bir DB bağlantısı
require_once 'auth_check.php';  // Tek bir auth kontrolü
```

---

## 📊 Veritabanı Tasarımı ve Contracts

### 1. **Relational Database Design (İlişkisel Veritabanı Tasarımı)**

Proje, **normalize edilmiş** bir veritabanı yapısı kullanır:

```
Bus_Company (Firma)
    ├── id (PRIMARY KEY)
    ├── name
    └── logo_path

User (Kullanıcı)
    ├── id (PRIMARY KEY)
    ├── full_name
    ├── email (UNIQUE)
    ├── role (user/company/admin)
    ├── company_id (FOREIGN KEY → Bus_Company)
    └── balance

Trips (Seferler)
    ├── id (PRIMARY KEY)
    ├── company_id (FOREIGN KEY → Bus_Company)
    ├── departure_city
    ├── destination_city
    ├── departure_time
    ├── arrival_time
    ├── price
    └── capacity

Tickets (Biletler)
    ├── id (PRIMARY KEY)
    ├── trip_id (FOREIGN KEY → Trips)
    ├── user_id (FOREIGN KEY → User)
    ├── status (active/cancelled/expired)
    └── total_price

Booked_Seats (Rezerve Koltuklar)
    ├── id (PRIMARY KEY)
    ├── ticket_id (FOREIGN KEY → Tickets)
    └── seat_number

Coupons (Kuponlar)
    ├── id (PRIMARY KEY)
    ├── code (UNIQUE)
    ├── discount
    ├── company_id (FOREIGN KEY → Bus_Company, NULLABLE)
    ├── usage_limit
    └── expire_date
```

### 2. **Database Contracts (Veritabanı Sözleşmeleri)**

#### **Foreign Key Constraints (Yabancı Anahtar Kısıtlamaları)**
```sql
FOREIGN KEY(company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE
FOREIGN KEY(trip_id) REFERENCES Trips(id)
FOREIGN KEY(user_id) REFERENCES User(id)
```

**Amaç:**
- ✅ Referans bütünlüğü (Referential Integrity)
- ✅ Veri tutarlılığı
- ✅ Cascade delete (Otomatik silme)

#### **Unique Constraints (Benzersizlik Kısıtlamaları)**
```sql
email TEXT UNIQUE NOT NULL
code TEXT UNIQUE NOT NULL
name TEXT UNIQUE NOT NULL
```

#### **NOT NULL Constraints (Boş Değer Kısıtlamaları)**
```sql
full_name TEXT NOT NULL
departure_city TEXT NOT NULL
price INTEGER NOT NULL
```

---

### 3. **Transaction Management (İşlem Yönetimi)**

Kritik işlemler, transaction içinde yapılır:

```php
// handle_purchase.php
try {
    $pdo->beginTransaction();
    
    // 1. Sefer kontrolü
    $stmt_trip = $pdo->prepare("SELECT ...");
    
    // 2. Kullanıcı bakiyesi kontrolü
    $stmt_user = $pdo->prepare("SELECT balance FROM User ...");
    
    // 3. Koltuk kontrolü
    $stmt_check_seats = $pdo->prepare("SELECT ...");
    
    // 4. Bilet oluşturma
    $stmt_insert_ticket = $pdo->prepare("INSERT INTO Tickets ...");
    
    // 5. Koltuk rezervasyonu
    $stmt_insert_seat = $pdo->prepare("INSERT INTO Booked_Seats ...");
    
    // 6. Bakiye güncelleme
    $stmt_update_balance = $pdo->prepare("UPDATE User SET balance = ? ...");
    
    $pdo->commit();  // Tüm işlemler başarılı
} catch (Exception $e) {
    $pdo->rollback();  // Hata durumunda geri al
    die("Hata: " . $e->getMessage());
}
```

**ACID Özellikleri:**
- ✅ **Atomicity**: Tüm işlemler ya başarılı ya da hiçbiri
- ✅ **Consistency**: Veritabanı tutarlılığı
- ✅ **Isolation**: İşlemler birbirinden izole
- ✅ **Durability**: Kalıcılık garantisi

---

## 🔒 Güvenlik Yaklaşımları

### 1. **Prepared Statements (Hazırlanmış Sorgular)**

Tüm SQL sorguları, **prepared statements** kullanır:

```php
// ❌ Güvensiz (SQL Injection riski)
$query = "SELECT * FROM User WHERE email = '$email'";

// ✅ Güvenli (Prepared Statement)
$stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
$stmt->execute([$email]);
```

**Avantajları:**
- ✅ SQL Injection koruması
- ✅ Performans optimizasyonu
- ✅ Otomatik escape

---

### 2. **Password Hashing (Şifre Hashleme)**

Kullanıcı şifreleri, **bcrypt** ile hashlenir:

```php
// handle_register.php
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// handle_login.php
if (password_verify($password, $user['password'])) {
    // Giriş başarılı
}
```

**Özellikler:**
- ✅ Tek yönlü hash
- ✅ Salt otomatik eklenir
- ✅ Güvenli algoritma (bcrypt)

---

### 3. **Session Management (Oturum Yönetimi)**

Kullanıcı oturumları, PHP session ile yönetilir:

```php
session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['balance'] = $user['balance'];
```

**Güvenlik Önlemleri:**
- ✅ Session ID güvenliği
- ✅ Oturum timeout
- ✅ Rol tabanlı erişim kontrolü

---

### 4. **Role-Based Access Control (RBAC)**

Sistem, **rol tabanlı erişim kontrolü** kullanır:

```php
// auth_check.php
function require_role($required_roles) {
    if (!in_array($_SESSION['role'], $required_roles)) {
        header('Location: /index.php');
        exit();
    }
}

// Kullanım:
require_role('company');  // Sadece firma yetkilileri
require_role('admin');   // Sadece admin
```

**Roller:**
- **user**: Bilet satın alma, iptal
- **company**: Sefer yönetimi
- **admin**: Sistem yönetimi

---

### 5. **Input Validation (Girdi Doğrulama)**

Form verileri, doğrulanır:

```php
// handle_login.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Not allowed method");
}

$email = $_POST['email'];
$password = $_POST['password'];
```

**HTML5 Validation:**
```html
<input type="email" name="email" required>
<input type="password" name="password" required>
```

---

### 6. **XSS (Cross-Site Scripting) Koruması**

Çıktılar, `htmlspecialchars()` ile escape edilir:

```php
echo htmlspecialchars($trip['departure_city']);
echo htmlspecialchars($_SESSION['full_name']);
```

---

## 🛠️ Teknoloji Stack

### **Backend**
- **PHP 8.2**: Server-side scripting
- **PDO**: Veritabanı erişim katmanı
- **SQLite**: Dosya tabanlı veritabanı
- **FPDF**: PDF oluşturma kütüphanesi

### **Frontend**
- **HTML5**: Semantic markup
- **CSS3**: Modern styling (animations, gradients, glassmorphism)
- **JavaScript**: Client-side interactivity
- **Responsive Design**: Mobile-first approach

### **Infrastructure**
- **Docker**: Containerization
- **Docker Compose**: Multi-container orchestration
- **Nginx**: Web server (reverse proxy)
- **PHP-FPM**: FastCGI Process Manager

---

## 🚀 Deployment ve DevOps

### 1. **Containerization (Konteynerleştirme)**

Proje, **Docker** ile containerize edilmiştir:

```dockerfile
# backend/Dockerfile
FROM php:8.2-fpm
WORKDIR /var/www/html
RUN docker-php-ext-install pdo pdo_sqlite

# frontend/Dockerfile
FROM nginx:alpine
```

**Avantajları:**
- ✅ Ortam tutarlılığı
- ✅ Kolay deployment
- ✅ İzolasyon

---

### 2. **Docker Compose Orchestration**

`docker-compose.yml` ile çoklu container yönetimi:

```yaml
services:
  backend:
    build: ./backend
    volumes:
      - ./backend/src:/var/www/html
      
  frontend:
    build: ./frontend
    ports:
      - "8080:80"
    depends_on:
      - backend
```

**Özellikler:**
- ✅ Service orchestration
- ✅ Volume mounting
- ✅ Network isolation
- ✅ Dependency management

---

### 3. **Volume Management**

Veriler, Docker volume'lar ile kalıcı hale getirilir:

```yaml
volumes:
  - ./backend/database:/var/www/html/database
  - ./backend/src:/var/www/html
  - ./frontend/public:/var/www/html/public
```

---

## 📈 Proje Metrikleri

### **Kod Organizasyonu**
- **Backend Dosyaları**: ~30+ PHP dosyası
- **Frontend Sayfaları**: 3 HTML sayfası (modern tasarım)
- **Veritabanı Tabloları**: 6 tablo
- **Rol Sayısı**: 3 (user, company, admin)

### **Güvenlik Özellikleri**
- ✅ Prepared Statements (SQL Injection koruması)
- ✅ Password Hashing (bcrypt)
- ✅ Session Management
- ✅ Role-Based Access Control
- ✅ XSS Protection (htmlspecialchars)
- ✅ Input Validation

### **Performans Optimizasyonları**
- ✅ Database Indexing (PRIMARY KEY, UNIQUE)
- ✅ Foreign Key Constraints
- ✅ Transaction Management
- ✅ Connection Pooling (PDO)

---

## 🎓 Öğrenilen Kavramlar

### **Tasarım Prensipleri**
1. **SOLID Principles** (kısmen)
   - Single Responsibility Principle
   - Separation of Concerns

2. **DRY (Don't Repeat Yourself)**
3. **KISS (Keep It Simple, Stupid)**
4. **YAGNI (You Aren't Gonna Need It)**

### **Yazılım Mimarileri**
1. **3-Tier Architecture**
2. **MVC Pattern** (kısmi)
3. **Modular Programming**
4. **Procedural Programming**

### **Güvenlik Best Practices**
1. **Prepared Statements**
2. **Password Hashing**
3. **Session Security**
4. **Input Validation**
5. **XSS Protection**

---

## 📝 Sonuç

Bu proje, **modern web geliştirme** prensiplerini kullanarak, **güvenli**, **ölçeklenebilir** ve **bakımı kolay** bir otobüs bileti satın alma platformu oluşturmuştur. Proje, **tasarım desenleri**, **güvenlik yaklaşımları** ve **modern teknolojiler** ile **kurumsal seviyede** bir yazılım geliştirme sürecini göstermektedir.

---

**Hazırlayan**: AI Assistant  
**Tarih**: 2024  
**Versiyon**: 1.0


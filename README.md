# OTOBİLET - Otobüs Bileti Satın Alma Platformu

Bu proje, PHP ve MySQL kullanılarak Docker üzerinde çalışan, N-Katmanlı mimari ile geliştirilmiş çok kullanıcılı bir otobüs bileti satış platformudur.

## Özellikler

- **Kullanıcı Sistemi:** Kayıt olma, giriş yapma, çıkış yapma ve oturum yönetimi.
- **Rol Yönetimi:** `user`, `company` ve `admin` olmak üzere üç farklı kullanıcı rolü.
- **Dinamik Arayüz:** Kullanıcının rolüne göre değişen menüler ve butonlar.
- **Sefer Yönetimi:**
  - Ziyaretçiler ve kullanıcılar için sefer arama ve listeleme.
  - `company` adminleri için kendi firmalarına ait seferleri yönetme (Ekleme/Silme/Düzenleme).
- **Admin Paneli:**
  - Otobüs firmalarını yönetme (CRUD).
  - Firma adminlerini (`company` rolü) yönetme (CRUD) ve firmalara atama.
- **Bilet İşlemleri:**
  - Koltuk seçerek bilet satın alma.
  - İndirim kuponu uygulama.
  - Bakiye sistemi üzerinden ödeme.
  - Bilet iptal etme (seferden 1 saat öncesine kadar) ve bakiye iadesi.
- **PDF Bilet:** Satın alınan biletleri PDF olarak indirme.

## Kullanılan Teknolojiler

- **Backend:** PHP 8.2
- **Veritabanı:** MySQL 8.0
- **Web Sunucusu:** Nginx
- **Mimari:** N-Katmanlı (Presentation / Business Layer / Data Access Layer)
- **Veritabanı Erişimi:** Stored Procedure, Function, Trigger
- **Paketleme ve Çalıştırma:** Docker & Docker Compose

## Mimari Yapı

```
src/
├── db/          → Veritabanı bağlantısı (Database.php)
├── dal/         → Data Access Layer - sadece Stored Procedure çağrıları
├── bl/          → Business Layer - iş mantığı
└── *.php        → Presentation Layer - arayüz ve yönlendirme
```

Tüm veritabanı işlemleri **SADECE** DAL katmanındaki Stored Procedure çağrıları üzerinden yapılmaktadır. Hiçbir katmanda doğrudan SQL komutu (SELECT/INSERT/UPDATE/DELETE) kullanılmamaktadır.

## Veritabanı Nesneleri

- **Stored Procedures:** Her tablo için Ekleme, Güncelleme, Silme, Listeleme işlemleri
- **Functions:** `fn_indirimli_fiyat`, `fn_dolu_koltuk_sayisi`
- **Triggers:** `trg_ticket_iptal_seat_sil`, `trg_ticket_ekle_kupon_duşur`

## Projeyi Çalıştırma

Bu projeyi yerel makinenizde çalıştırmak için Docker ve Docker Compose'un yüklü olması gerekmektedir.

**1. Projeyi Klonlayın:**
```bash
git clone https://github.com/Maysecc/bilet-satin-alma.git
cd bilet-satin-alma
```

**2. Docker Container'larını Başlatın:**
```bash
docker-compose up -d --build
```

Veritabanı tabloları, Stored Procedure'lar, Function'lar ve Trigger'lar `init.sql` aracılığıyla **otomatik olarak** oluşturulur. Ek bir kurulum adımı gerekmez.

**3. Uygulamaya Erişin:**

Tarayıcınızı açın ve `http://localhost:8080` adresine gidin.

**4. (İsteğe Bağlı) phpMyAdmin:**

`http://localhost:8081` adresinden veritabanını görsel olarak inceleyebilirsiniz.

## Test Kullanıcıları

Uygulama ilk açıldığında aşağıdaki test kullanıcıları otomatik olarak oluşturulur:

- **Admin (Sistem Yöneticisi):**
  - E-posta: `admin@gmail.com`
  - Şifre: `123456`

- **Company Admin (Firma Yetkilisi):**
  - E-posta: `metrotest@gmail.com`
  - Şifre: `123456`

- **Kullanıcı:**
  - E-posta: `ali@gmail.com`
  - Şifre: `123456`

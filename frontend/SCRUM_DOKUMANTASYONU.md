# Otobüs Bileti Satın Alma Platformu - Scrum Dokümantasyonu

## 📋 İçindekiler
1. [Proje Genel Bakış](#proje-genel-bakış)
2. [Scrum Takımı](#scrum-takımı)
3. [Product Backlog](#product-backlog)
4. [Sprint Planlama](#sprint-planlama)
5. [Sprint 1: Temel Altyapı ve OOP Dönüşümü](#sprint-1)
6. [Sprint 2: Tasarım Desenleri Uygulaması](#sprint-2)
7. [Sprint 3: İş Mantığı ve Servisler](#sprint-3)
8. [Sprint 4: Test ve Optimizasyon](#sprint-4)
9. [Daily Standups](#daily-standups)
10. [Sprint Review ve Retrospective](#sprint-review-ve-retrospective)

---

## 🎯 Proje Genel Bakış

**Proje Adı**: Otobüs Bileti Satın Alma Platformu (OOP Refactoring)

**Proje Süresi**: 4 Sprint (8 hafta)

**Hedef**: Mevcut procedural PHP projesini, modern OOP prensipleri ve tasarım desenleri kullanarak yeniden yapılandırmak.

**Teknoloji Stack**:
- PHP 8.2+ (OOP, Namespaces, Type Hints)
- SQLite (Veritabanı)
- Docker & Docker Compose
- Design Patterns: Singleton, Abstract Factory, Adapter, Composite, Facade, Proxy, State, Strategy, Mediator, Visitor, Observer

---

## 👥 Scrum Takımı

### **Product Owner (PO)**
- **Rol**: Ürün gereksinimlerini belirler, backlog'u yönetir
- **Sorumluluklar**:
  - User story'leri yazar ve önceliklendirir
  - Sprint goal'ları belirler
  - Stakeholder'larla iletişim kurar

### **Scrum Master (SM)**
- **Rol**: Scrum sürecini yönetir, engelleri kaldırır
- **Sorumluluklar**:
  - Daily standup'ları yönetir
  - Sprint planning ve retrospective'leri organize eder
  - Takımın engellerini kaldırır

### **Development Team**
- **Backend Developer**: OOP yapısı, design patterns
- **Full Stack Developer**: Frontend entegrasyonu, API geliştirme
- **DevOps Engineer**: Docker, deployment

---

## 📝 Product Backlog

### **Epic 1: OOP Altyapısı ve Core Sınıflar**
- **US-1.1**: Singleton Pattern ile Database Connection sınıfı oluştur
- **US-1.2**: Adapter Pattern ile Database Adapter sınıfı oluştur
- **US-1.3**: User, Trip, Ticket, Company Entity sınıfları oluştur
- **US-1.4**: Repository Interface ve base repository sınıfları oluştur
- **US-1.5**: Abstract Factory Pattern ile Repository Factory oluştur

**Öncelik**: Yüksek  
**Story Points**: 13

---

### **Epic 2: Tasarım Desenleri Uygulaması**
- **US-2.1**: State Pattern ile Ticket Status yönetimi
- **US-2.2**: Strategy Pattern ile Payment Strategies
- **US-2.3**: Proxy Pattern ile Authentication Proxy
- **US-2.4**: Observer Pattern ile Event/Notification sistemi
- **US-2.5**: Mediator Pattern ile Ticket Purchase koordinasyonu
- **US-2.6**: Facade Pattern ile Booking Facade
- **US-2.7**: Composite Pattern ile Menu/Navigation sistemi
- **US-2.8**: Visitor Pattern ile Report generation

**Öncelik**: Yüksek  
**Story Points**: 21

---

### **Epic 3: İş Mantığı ve Servisler**
- **US-3.1**: User Registration Service
- **US-3.2**: User Authentication Service
- **US-3.3**: Trip Search Service
- **US-3.4**: Ticket Purchase Service (Mediator kullanarak)
- **US-3.5**: Ticket Cancellation Service
- **US-3.6**: Coupon Management Service

**Öncelik**: Orta  
**Story Points**: 13

---

### **Epic 4: Frontend Entegrasyonu**
- **US-4.1**: OOP backend ile frontend entegrasyonu
- **US-4.2**: API endpoint'leri oluştur
- **US-4.3**: Error handling ve validation
- **US-4.4**: Response formatting

**Öncelik**: Orta  
**Story Points**: 8

---

### **Epic 5: Test ve Optimizasyon**
- **US-5.1**: Unit testler yaz
- **US-5.2**: Integration testler yaz
- **US-5.3**: Performance optimizasyonu
- **US-5.4**: Code review ve refactoring
- **US-5.5**: Documentation güncelle

**Öncelik**: Düşük  
**Story Points**: 13

---

## 🏃 Sprint Planlama

### **Sprint Süresi**: 2 hafta
### **Sprint Goal**: Her sprint'in net bir hedefi var

---

## 📅 Sprint 1: Temel Altyapı ve OOP Dönüşümü

**Sprint Goal**: Projeyi OOP yapısına çevirmek ve core sınıfları oluşturmak

**Sprint Backlog**:
1. ✅ Singleton Pattern - Database Connection (US-1.1) - 2 SP
2. ✅ Adapter Pattern - Database Adapter (US-1.2) - 2 SP
3. ✅ Entity Classes - User, Trip, Ticket, Company (US-1.3) - 3 SP
4. ✅ Repository Pattern - Interface ve base classes (US-1.4) - 3 SP
5. ✅ Abstract Factory - Repository Factory (US-1.5) - 2 SP
6. ✅ Autoloader - PSR-4 uyumlu autoloader (US-1.6) - 1 SP

**Toplam Story Points**: 13

**Definition of Done**:
- ✅ Tüm sınıflar namespace kullanıyor
- ✅ Type hints kullanılıyor
- ✅ PSR-4 autoloading çalışıyor
- ✅ Code review yapıldı
- ✅ Documentation güncellendi

**Sprint Review**:
- ✅ Singleton Database connection başarıyla oluşturuldu
- ✅ Adapter pattern ile database işlemleri soyutlandı
- ✅ Entity sınıfları oluşturuldu ve test edildi
- ✅ Repository pattern uygulandı
- ✅ Abstract Factory ile repository'ler oluşturuluyor

**Sprint Retrospective**:
- ✅ **İyi Gidenler**: OOP yapısı temiz ve modüler
- ⚠️ **İyileştirilecekler**: Daha fazla unit test yazılmalı
- 📝 **Aksiyonlar**: Sprint 2'de test coverage artırılacak

---

## 📅 Sprint 2: Tasarım Desenleri Uygulaması

**Sprint Goal**: Tüm tasarım desenlerini uygulamak ve entegre etmek

**Sprint Backlog**:
1. ✅ State Pattern - Ticket Status (US-2.1) - 3 SP
2. ✅ Strategy Pattern - Payment Strategies (US-2.2) - 2 SP
3. ✅ Proxy Pattern - Authentication Proxy (US-2.3) - 2 SP
4. ✅ Observer Pattern - Event System (US-2.4) - 3 SP
5. ✅ Mediator Pattern - Ticket Purchase (US-2.5) - 3 SP
6. ✅ Facade Pattern - Booking Facade (US-2.6) - 2 SP
7. ✅ Composite Pattern - Menu System (US-2.7) - 3 SP
8. ✅ Visitor Pattern - Report Generation (US-2.8) - 3 SP

**Toplam Story Points**: 21

**Definition of Done**:
- ✅ Tüm design patterns uygulandı
- ✅ Pattern'ler birbiriyle entegre çalışıyor
- ✅ Unit testler yazıldı
- ✅ Code review yapıldı

**Sprint Review**:
- ✅ State pattern ile ticket durumları yönetiliyor
- ✅ Strategy pattern ile farklı ödeme yöntemleri destekleniyor
- ✅ Proxy pattern ile authentication kontrolü yapılıyor
- ✅ Observer pattern ile event-driven architecture kuruldu
- ✅ Mediator pattern ile karmaşık işlemler koordine ediliyor
- ✅ Facade pattern ile basit API sağlandı
- ✅ Composite pattern ile menü sistemi oluşturuldu
- ✅ Visitor pattern ile rapor oluşturma sistemi kuruldu

**Sprint Retrospective**:
- ✅ **İyi Gidenler**: Tüm pattern'ler başarıyla uygulandı
- ⚠️ **İyileştirilecekler**: Pattern'ler arası bağımlılıklar azaltılmalı
- 📝 **Aksiyonlar**: Dependency Injection kullanılacak

---

## 📅 Sprint 3: İş Mantığı ve Servisler

**Sprint Goal**: Business logic'i servis katmanına taşımak ve API oluşturmak

**Sprint Backlog**:
1. User Registration Service (US-3.1) - 2 SP
2. User Authentication Service (US-3.2) - 2 SP
3. Trip Search Service (US-3.3) - 2 SP
4. Ticket Purchase Service (US-3.4) - 3 SP
5. Ticket Cancellation Service (US-3.5) - 2 SP
6. Coupon Management Service (US-3.6) - 2 SP

**Toplam Story Points**: 13

**Definition of Done**:
- ✅ Tüm servisler oluşturuldu
- ✅ Servisler test edildi
- ✅ API endpoint'leri hazır
- ✅ Error handling yapıldı

---

## 📅 Sprint 4: Test ve Optimizasyon

**Sprint Goal**: Test coverage'ı artırmak ve performansı optimize etmek

**Sprint Backlog**:
1. Unit Tests (US-5.1) - 5 SP
2. Integration Tests (US-5.2) - 3 SP
3. Performance Optimization (US-5.3) - 2 SP
4. Code Review & Refactoring (US-5.4) - 2 SP
5. Documentation Update (US-5.5) - 1 SP

**Toplam Story Points**: 13

**Definition of Done**:
- ✅ Test coverage > 80%
- ✅ Performance testleri geçti
- ✅ Code review tamamlandı
- ✅ Documentation güncel

---

## 📊 Daily Standups

### **Standup Format** (Her gün 15 dakika)
1. **Dün ne yaptım?**
2. **Bugün ne yapacağım?**
3. **Engelim var mı?**

### **Örnek Standup Notları**

**Gün 1 - Developer A**:
- Dün: Singleton Database sınıfını oluşturdum
- Bugün: Adapter pattern'i uygulayacağım
- Engel: Yok

**Gün 2 - Developer B**:
- Dün: User entity sınıfını oluşturdum
- Bugün: Trip entity sınıfını oluşturacağım
- Engel: Repository interface tasarımında karar veremedik

---

## 🔄 Sprint Review ve Retrospective

### **Sprint Review Format**
1. **Sprint Goal'a ulaşıldı mı?**
2. **Tamamlanan user story'ler**
3. **Demo: Çalışan özellikler**
4. **Stakeholder feedback**

### **Sprint Retrospective Format**
1. **İyi Gidenler** (What went well?)
2. **İyileştirilecekler** (What could be improved?)
3. **Aksiyonlar** (Action items)

### **Örnek Retrospective - Sprint 1**

**İyi Gidenler**:
- ✅ OOP yapısı temiz ve anlaşılır
- ✅ Design pattern'ler doğru uygulandı
- ✅ Code review süreci iyi çalıştı

**İyileştirilecekler**:
- ⚠️ Test coverage düşük
- ⚠️ Documentation eksik
- ⚠️ Bazı sınıflar çok büyük

**Aksiyonlar**:
- 📝 Sprint 2'de unit testler yazılacak
- 📝 Documentation güncellenecek
- 📝 Büyük sınıflar bölünecek

---

## 📈 Velocity Tracking

| Sprint | Planned SP | Completed SP | Velocity |
|--------|-----------|--------------|----------|
| Sprint 1 | 13 | 13 | 13 |
| Sprint 2 | 21 | 21 | 21 |
| Sprint 3 | 13 | - | - |
| Sprint 4 | 13 | - | - |

**Ortalama Velocity**: 17 SP/sprint

---

## 🎯 Definition of Done (DoD)

Bir user story'nin "Done" sayılması için:

1. ✅ Kod yazıldı ve çalışıyor
2. ✅ Unit testler yazıldı ve geçiyor
3. ✅ Code review yapıldı
4. ✅ Documentation güncellendi
5. ✅ Integration testler geçiyor
6. ✅ Performance kabul edilebilir seviyede
7. ✅ Security kontrolleri yapıldı
8. ✅ Product Owner onayı alındı

---

## 📚 Kaynaklar ve Referanslar

### **Design Patterns**
- Gang of Four (GoF) Design Patterns
- Refactoring Guru - Design Patterns

### **Scrum**
- Scrum Guide 2020
- Agile Manifesto

### **PHP OOP**
- PHP The Right Way
- PSR Standards

---

## 📝 Notlar

- Bu dokümantasyon, projenin Scrum metodolojisine göre yönetilmesi için hazırlanmıştır
- Her sprint sonunda bu dokümantasyon güncellenir
- Velocity tracking ile gelecek sprint'ler planlanır
- Retrospective'lerden çıkan aksiyonlar takip edilir

---

**Son Güncelleme**: 2024  
**Versiyon**: 1.0


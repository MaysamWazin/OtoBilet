<?php

// docker-compose exec --user www-data backend php db_setup.php
// docker-compose exec --user www-data backend php fake_seed.php
echo "<pre>"; 


$db_path = __DIR__ . '/database/bilet_sistemi.sqlite';

try {
   
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Veritabanı dosyasına başarıyla bağlanıldı: $db_path\n";

    
    $sql = "
        PRAGMA foreign_keys = ON;

        CREATE TABLE IF NOT EXISTS Bus_Company (
            id TEXT PRIMARY KEY,
            name TEXT UNIQUE NOT NULL,
            logo_path TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS User (
            id TEXT PRIMARY KEY,
            full_name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            role TEXT NOT NULL,
            password TEXT NOT NULL,
            company_id TEXT,
            balance REAL DEFAULT 1000.0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS Trips (
            id TEXT PRIMARY KEY,
            company_id TEXT NOT NULL,
            departure_city TEXT NOT NULL, 
            destination_city TEXT NOT NULL,
            departure_time DATETIME NOT NULL,
            arrival_time DATETIME NOT NULL,
            price INTEGER NOT NULL,
            capacity INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE
        );;

        -- GÜNCELLENEN TABLO BURASI --
        CREATE TABLE IF NOT EXISTS Coupons (
            id TEXT PRIMARY KEY,
            code TEXT UNIQUE NOT NULL,
            discount REAL NOT NULL,
            company_id TEXT, -- YENİ EKLENEN SÜTUN
            usage_limit INTEGER NOT NULL,
            expire_date DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(company_id) REFERENCES Bus_Company(id) -- YENİ EKLENEN İLİŞKİ
        );

        CREATE TABLE IF NOT EXISTS Tickets (
            id TEXT PRIMARY KEY,
            trip_id TEXT NOT NULL,
            user_id TEXT NOT NULL,
            status TEXT DEFAULT 'active' NOT NULL, -- active, cancelled, expired
            total_price INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(trip_id) REFERENCES Trips(id),
            FOREIGN KEY(user_id) REFERENCES User(id)
        );
        
        CREATE TABLE IF NOT EXISTS Booked_Seats (
            id TEXT PRIMARY KEY,
            ticket_id TEXT NOT NULL,
            seat_number INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(ticket_id) REFERENCES Tickets(id)
        );

        CREATE TABLE IF NOT EXISTS User_Coupons (
            id TEXT PRIMARY KEY,
            coupon_id TEXT NOT NULL,
            user_id TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(coupon_id) REFERENCES Coupons(id),
            FOREIGN KEY(user_id) REFERENCES User(id)
        );
    ";

   
    $pdo->exec($sql);
    echo "Tüm tablolar YENİ ŞEMA ile başarıyla oluşturuldu.\n";

} catch (PDOException $e) {
    
    die("Veritabanı hatası: " . $e->getMessage());
}

echo "</pre>";
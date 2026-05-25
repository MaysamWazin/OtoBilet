-- ============================================================
-- OTOBİLET - MySQL Veritabanı
-- ============================================================

USE otobilet_db;

-- ----------------------------
-- TABLOLAR
-- ----------------------------

CREATE TABLE IF NOT EXISTS Bus_Company (
    id VARCHAR(64) PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    logo_path TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS User (
    id VARCHAR(64) PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    password VARCHAR(255) NOT NULL,
    company_id VARCHAR(64) DEFAULT NULL,
    balance DECIMAL(10,2) DEFAULT 1000.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Trips (
    id VARCHAR(64) PRIMARY KEY,
    company_id VARCHAR(64) NOT NULL,
    departure_city VARCHAR(100) NOT NULL,
    destination_city VARCHAR(100) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL DEFAULT 40,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Coupons (
    id VARCHAR(64) PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount DECIMAL(5,2) NOT NULL,
    company_id VARCHAR(64) DEFAULT NULL,
    usage_limit INT NOT NULL DEFAULT 100,
    expire_date DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Tickets (
    id VARCHAR(64) PRIMARY KEY,
    trip_id VARCHAR(64) NOT NULL,
    user_id VARCHAR(64) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    total_price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES Trips(id),
    FOREIGN KEY (user_id) REFERENCES User(id)
);

CREATE TABLE IF NOT EXISTS Booked_Seats (
    id VARCHAR(64) PRIMARY KEY,
    ticket_id VARCHAR(64) NOT NULL,
    seat_number INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES Tickets(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS User_Coupons (
    id VARCHAR(64) PRIMARY KEY,
    coupon_id VARCHAR(64) NOT NULL,
    user_id VARCHAR(64) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES Coupons(id),
    FOREIGN KEY (user_id) REFERENCES User(id)
);

-- ----------------------------
-- STORED PROCEDURES - USER
-- ----------------------------

DELIMITER $$
CREATE PROCEDURE sp_user_sifre_guncelle(IN p_id VARCHAR(64), IN p_sifre VARCHAR(255))
BEGIN
    UPDATE User SET password = p_sifre WHERE id = p_id;
END$$

CREATE PROCEDURE sp_user_ekle(
    IN p_id VARCHAR(64), IN p_full_name VARCHAR(150),
    IN p_email VARCHAR(150), IN p_role VARCHAR(20), IN p_password VARCHAR(255)
)
BEGIN
    INSERT INTO User(id, full_name, email, role, password)
    VALUES(p_id, p_full_name, p_email, p_role, p_password);
END$$

CREATE PROCEDURE sp_user_guncelle(
    IN p_id VARCHAR(64), IN p_full_name VARCHAR(150), IN p_email VARCHAR(150)
)
BEGIN
    UPDATE User SET full_name=p_full_name, email=p_email WHERE id=p_id;
END$$

CREATE PROCEDURE sp_user_sil(IN p_id VARCHAR(64))
BEGIN
    DELETE FROM User WHERE id=p_id;
END$$

CREATE PROCEDURE sp_user_listele()
BEGIN
    SELECT id, full_name, email, role, company_id, balance, created_at FROM User;
END$$

CREATE PROCEDURE sp_user_email_getir(IN p_email VARCHAR(150))
BEGIN
    SELECT * FROM User WHERE email=p_email LIMIT 1;
END$$

CREATE PROCEDURE sp_user_id_getir(IN p_id VARCHAR(64))
BEGIN
    SELECT * FROM User WHERE id=p_id LIMIT 1;
END$$

CREATE PROCEDURE sp_user_bakiye_guncelle(IN p_id VARCHAR(64), IN p_bakiye DECIMAL(10,2))
BEGIN
    UPDATE User SET balance=p_bakiye WHERE id=p_id;
END$$

CREATE PROCEDURE sp_user_company_ata(IN p_id VARCHAR(64), IN p_company_id VARCHAR(64))
BEGIN
    UPDATE User SET company_id=p_company_id, role='company' WHERE id=p_id;
END$$

-- ----------------------------
-- STORED PROCEDURES - BUS_COMPANY
-- ----------------------------

CREATE PROCEDURE sp_company_ekle(
    IN p_id VARCHAR(64), IN p_name VARCHAR(150), IN p_logo TEXT
)
BEGIN
    INSERT INTO Bus_Company(id, name, logo_path) VALUES(p_id, p_name, p_logo);
END$$

CREATE PROCEDURE sp_company_guncelle(
    IN p_id VARCHAR(64), IN p_name VARCHAR(150), IN p_logo TEXT
)
BEGIN
    UPDATE Bus_Company SET name=p_name, logo_path=p_logo WHERE id=p_id;
END$$

CREATE PROCEDURE sp_company_sil(IN p_id VARCHAR(64))
BEGIN
    DELETE FROM Bus_Company WHERE id=p_id;
END$$

CREATE PROCEDURE sp_company_listele()
BEGIN
    SELECT * FROM Bus_Company ORDER BY name;
END$$

CREATE PROCEDURE sp_company_id_getir(IN p_id VARCHAR(64))
BEGIN
    SELECT * FROM Bus_Company WHERE id=p_id LIMIT 1;
END$$

-- ----------------------------
-- STORED PROCEDURES - TRIPS
-- ----------------------------

CREATE PROCEDURE sp_trip_ekle(
    IN p_id VARCHAR(64), IN p_company_id VARCHAR(64),
    IN p_departure VARCHAR(100), IN p_destination VARCHAR(100),
    IN p_dep_time DATETIME, IN p_arr_time DATETIME,
    IN p_price DECIMAL(10,2), IN p_capacity INT
)
BEGIN
    INSERT INTO Trips(id, company_id, departure_city, destination_city,
                      departure_time, arrival_time, price, capacity)
    VALUES(p_id, p_company_id, p_departure, p_destination,
           p_dep_time, p_arr_time, p_price, p_capacity);
END$$

CREATE PROCEDURE sp_trip_guncelle(
    IN p_id VARCHAR(64), IN p_price DECIMAL(10,2), IN p_capacity INT
)
BEGIN
    UPDATE Trips SET price=p_price, capacity=p_capacity WHERE id=p_id;
END$$

CREATE PROCEDURE sp_trip_sil(IN p_id VARCHAR(64))
BEGIN
    DELETE FROM Trips WHERE id=p_id;
END$$

CREATE PROCEDURE sp_trip_listele()
BEGIN
    SELECT t.*, c.name AS company_name FROM Trips t
    JOIN Bus_Company c ON t.company_id = c.id
    ORDER BY t.departure_time ASC;
END$$

CREATE PROCEDURE sp_trip_ara(IN p_dep VARCHAR(100), IN p_dest VARCHAR(100))
BEGIN
    SELECT t.*, c.name AS company_name FROM Trips t
    JOIN Bus_Company c ON t.company_id = c.id
    WHERE t.departure_city LIKE CONCAT('%', p_dep, '%')
      AND t.destination_city LIKE CONCAT('%', p_dest, '%')
      AND t.departure_time > NOW()
    ORDER BY t.departure_time ASC;
END$$

CREATE PROCEDURE sp_trip_id_getir(IN p_id VARCHAR(64))
BEGIN
    SELECT t.*, c.name AS company_name FROM Trips t
    JOIN Bus_Company c ON t.company_id = c.id
    WHERE t.id = p_id LIMIT 1;
END$$

CREATE PROCEDURE sp_trip_firma_listele(IN p_company_id VARCHAR(64))
BEGIN
    SELECT * FROM Trips WHERE company_id=p_company_id ORDER BY departure_time DESC;
END$$

-- ----------------------------
-- STORED PROCEDURES - COUPONS
-- ----------------------------

CREATE PROCEDURE sp_coupon_ekle(
    IN p_id VARCHAR(64), IN p_code VARCHAR(50), IN p_discount DECIMAL(5,2),
    IN p_company_id VARCHAR(64), IN p_limit INT, IN p_expire DATETIME
)
BEGIN
    INSERT INTO Coupons(id, code, discount, company_id, usage_limit, expire_date)
    VALUES(p_id, p_code, p_discount, p_company_id, p_limit, p_expire);
END$$

CREATE PROCEDURE sp_coupon_guncelle(
    IN p_id VARCHAR(64), IN p_limit INT, IN p_expire DATETIME
)
BEGIN
    UPDATE Coupons SET usage_limit=p_limit, expire_date=p_expire WHERE id=p_id;
END$$

CREATE PROCEDURE sp_coupon_sil(IN p_id VARCHAR(64))
BEGIN
    DELETE FROM Coupons WHERE id=p_id;
END$$

CREATE PROCEDURE sp_coupon_listele()
BEGIN
    SELECT * FROM Coupons ORDER BY expire_date DESC;
END$$

CREATE PROCEDURE sp_coupon_kod_getir(IN p_code VARCHAR(50), IN p_company_id VARCHAR(64))
BEGIN
    SELECT * FROM Coupons
    WHERE code = p_code
      AND expire_date > NOW()
      AND usage_limit > 0
      AND (company_id IS NULL OR company_id = p_company_id)
    LIMIT 1;
END$$

CREATE PROCEDURE sp_coupon_limit_duşur(IN p_id VARCHAR(64))
BEGIN
    UPDATE Coupons SET usage_limit = usage_limit - 1 WHERE id = p_id;
END$$

-- ----------------------------
-- STORED PROCEDURES - TICKETS
-- ----------------------------

CREATE PROCEDURE sp_ticket_ekle(
    IN p_id VARCHAR(64), IN p_trip_id VARCHAR(64),
    IN p_user_id VARCHAR(64), IN p_total_price DECIMAL(10,2)
)
BEGIN
    INSERT INTO Tickets(id, trip_id, user_id, total_price)
    VALUES(p_id, p_trip_id, p_user_id, p_total_price);
END$$

CREATE PROCEDURE sp_ticket_iptal(IN p_id VARCHAR(64))
BEGIN
    UPDATE Tickets SET status='cancelled' WHERE id=p_id;
END$$

CREATE PROCEDURE sp_ticket_listele_user(IN p_user_id VARCHAR(64))
BEGIN
    SELECT tk.*, tr.departure_city, tr.destination_city,
           tr.departure_time, tr.arrival_time, tr.price,
           c.name AS company_name
    FROM Tickets tk
    JOIN Trips tr ON tk.trip_id = tr.id
    JOIN Bus_Company c ON tr.company_id = c.id
    WHERE tk.user_id = p_user_id
    ORDER BY tk.created_at DESC;
END$$

CREATE PROCEDURE sp_ticket_id_getir(IN p_id VARCHAR(64))
BEGIN
    SELECT tk.*, tr.departure_city, tr.destination_city,
           tr.departure_time, tr.arrival_time,
           c.name AS company_name,
           u.full_name, u.email
    FROM Tickets tk
    JOIN Trips tr ON tk.trip_id = tr.id
    JOIN Bus_Company c ON tr.company_id = c.id
    JOIN User u ON tk.user_id = u.id
    WHERE tk.id = p_id LIMIT 1;
END$$

-- ----------------------------
-- STORED PROCEDURES - BOOKED_SEATS
-- ----------------------------

CREATE PROCEDURE sp_seat_ekle(
    IN p_id VARCHAR(64), IN p_ticket_id VARCHAR(64), IN p_seat_number INT
)
BEGIN
    INSERT INTO Booked_Seats(id, ticket_id, seat_number)
    VALUES(p_id, p_ticket_id, p_seat_number);
END$$

CREATE PROCEDURE sp_seat_sil_ticket(IN p_ticket_id VARCHAR(64))
BEGIN
    DELETE FROM Booked_Seats WHERE ticket_id=p_ticket_id;
END$$

CREATE PROCEDURE sp_seat_dolu_getir(IN p_trip_id VARCHAR(64))
BEGIN
    SELECT bs.seat_number FROM Booked_Seats bs
    JOIN Tickets t ON bs.ticket_id = t.id
    WHERE t.trip_id = p_trip_id AND t.status = 'active';
END$$

-- ----------------------------
-- FUNCTION 1: İndirimli toplam fiyat hesapla
-- ----------------------------

CREATE FUNCTION fn_indirimli_fiyat(p_fiyat DECIMAL(10,2), p_indirim DECIMAL(5,2))
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    RETURN p_fiyat - (p_fiyat * p_indirim / 100);
END$$

-- ----------------------------
-- FUNCTION 2: Seferdeki dolu koltuk sayısı
-- ----------------------------

CREATE FUNCTION fn_dolu_koltuk_sayisi(p_trip_id VARCHAR(64))
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_sayi INT;
    SELECT COUNT(*) INTO v_sayi
    FROM Booked_Seats bs
    JOIN Tickets t ON bs.ticket_id = t.id
    WHERE t.trip_id = p_trip_id AND t.status = 'active';
    RETURN v_sayi;
END$$

-- ----------------------------
-- TRIGGER 1: Bilet iptalinde koltukları serbest bırak
-- ----------------------------

CREATE TRIGGER trg_ticket_iptal_seat_sil
AFTER UPDATE ON Tickets
FOR EACH ROW
BEGIN
    IF NEW.status = 'cancelled' AND OLD.status = 'active' THEN
        DELETE FROM Booked_Seats WHERE ticket_id = NEW.id;
    END IF;
END$$

-- ----------------------------
-- TRIGGER 2: Kupon kullanılınca limiti düşür
-- ----------------------------
DELIMITER ;
-- YENİ EKLENECEK KISIM --
-- ----------------------------
-- EVENT 1: Süresi geçmiş biletleri otomatik iptal et (Her Saat Başı)
-- ----------------------------
CREATE EVENT IF NOT EXISTS ev_biletleri_gecersiz_yap
ON SCHEDULE EVERY 1 HOUR
DO
  UPDATE Tickets 
  SET status = 'expired'
  WHERE status = 'active' 
    AND trip_id IN (SELECT id FROM Trips WHERE departure_time < NOW());


-- ----------------------------
-- TEST VERİLERİ
-- ----------------------------

INSERT IGNORE INTO Bus_Company(id, name) VALUES
('company_metro', 'Metro Turizm'),
('company_uludag', 'Uludağ Ekspres');

INSERT IGNORE INTO User(id, full_name, email, role, password, balance) VALUES
('user_admin', 'Admin Kullanıcı', 'admin@gmail.com', 'admin',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1000.00),
('user_company1', 'Metro Yetkili', 'metrotest@gmail.com', 'company',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 500.00),
('user_ali', 'Ali Yılmaz', 'ali@gmail.com', 'user',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 300.00);
-- Şifre: "password"

UPDATE User SET company_id='company_metro' WHERE id='user_company1';

INSERT IGNORE INTO Coupons(id, code, discount, usage_limit, expire_date) VALUES
('coupon_1', 'HOSGELDIN10', 10.00, 50, '2026-12-31'),
('coupon_2', 'YILBASI20', 20.00, 100, '2026-12-31');

<?php
//docker-compose exec --user www-data backend php db_setup.php
//docker-compose exec --user www-data backend php fake_seed.php
require_once 'db.php';
echo "<pre>";

try {
    $company_id = 'company_' . uniqid();
    $company_name = 'Metro Turizm';
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO Bus_Company (id, name) VALUES (?, ?)");
    $stmt->execute([$company_id, $company_name]);
    echo "Firma oluşturuldu: $company_name\n";


    $coupon1_id = 'coupon_' . uniqid();
    $pdo->prepare(
        "INSERT OR IGNORE INTO Coupons (id, code, discount, company_id, usage_limit, expire_date) 
         VALUES (?, ?, ?, ?, ?, ?)"
    )->execute([
        $coupon1_id,
        'YENIUYE10', 
        10,          
        NULL,       
        100,        
        '2026-12-31 23:59:59' 
    ]);
    echo "Genel kupon oluşturuldu: YENIUYE10\n";

    $coupon2_id = 'coupon_' . uniqid();
    $pdo->prepare(
        "INSERT OR IGNORE INTO Coupons (id, code, discount, company_id, usage_limit, expire_date) 
         VALUES (?, ?, ?, ?, ?, ?)"
    )->execute([
        $coupon2_id,
        'YAVUZLAR15', 
        15,          
        NULL, 
        50,          
        '2026-12-31 23:59:59' 
    ]);
    echo "Firmaya özel kupon oluşturuldu: YAVUZLAR15\n";

    $fake_comp_admin_id = 'compadmin_' . uniqid();
    $admin_email = 'metrotest@gmail.com';
    $admin_password = password_hash('test123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO User (id, full_name, email, role, password, company_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fake_comp_admin_id, 'Company Admin', $admin_email, 'company', $admin_password, $company_id]);
    echo "Company Admin başarı ile oluşturuldu: $admin_email\n";

    $super_admin_id = 'user_' . uniqid();
    $super_admin_email = 'admin@gmail.com';
    $super_admin_password = password_hash('admin123', PASSWORD_BCRYPT);

    $stmt = $pdo -> prepare("INSERT OR IGNORE INTO User (id, full_name, email, role, password) VALUES (?, ?, ?, ?, ?)");
    $stmt ->execute([$super_admin_id, 'Süper Admin', $super_admin_email, 'admin', $super_admin_password]);

    echo "Süper Admin Eklendi $super_admin_email";

   
    $trips = [
       
        ['Ankara', 'İstanbul', '2025-10-16 13:00:00', '2025-10-16 19:00:00', 450, 40],
        ['Giresun', 'Trabzon', '2025-10-16 06:00:00', '2025-10-16 20:00:00', 250, 40]
    ];
    
    
    $stmt = $pdo->prepare(
        "INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    foreach ($trips as $trip) {
        $trip_id = 'trip_' . uniqid();
        

        $stmt->execute([
            $trip_id, 
            $company_id, 
            $trip[0], 
            $trip[1], 
            $trip[2], 
            $trip[3], 
            $trip[4], 
            $trip[5]  
        ]);

        echo "Sefer oluşturuldu: {$trip[0]} -> {$trip[1]}\n";
    }

    echo "\nVeritabanı sahte verilerle başarıyla dolduruldu.\n";

} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

echo "</pre>";
?>
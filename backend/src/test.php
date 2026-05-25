<?php


echo "<h1>Merhaba Dünya! Burası Backend (PHP).</h1>";


try {
    
    $db_path = __DIR__ . '/database/bilet_sistemi.sqlite';
    
    
    $pdo = new PDO('sqlite:' . $db_path);
    
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green;'>SQLite veritabanına başarıyla bağlanıldı!</p>";
    echo "<p>Veritabanı Dosya Yolu: " . $db_path . "</p>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Veritabanı bağlantı hatası: " . $e->getMessage() . "</p>";
    echo "<p>Not: Henüz 'bilet_sistemi.sqlite' dosyasını oluşturmamış olabilirsiniz. /backend/database/ klasörüne boş bir dosya eklemeniz yeterlidir.</p>";
}

echo "<hr>";


echo "<p><b>Oluşturulan Tabloların Listesi:</b></p>";
$query = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';");
$tables = $query->fetchAll(PDO::FETCH_COLUMN);
echo "<ul>";
foreach ($tables as $table) {
    echo "<li>" . htmlspecialchars($table) . "</li>";
}
echo "</ul>";

echo "<hr>";

//phpinfo();
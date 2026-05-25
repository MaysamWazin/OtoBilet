<?php
// ============================================================
// ESKI db.php'nin YENİ HALİ - MySQL bağlantısı
// SQLite yerine Database.php singleton'ı kullanıyoruz
// ============================================================
require_once __DIR__ . '/db/Database.php';

$pdo = Database::getInstance()->getPDO();

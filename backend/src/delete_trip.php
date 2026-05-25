<?php
require_once 'auth_check.php';
require_role('company');

require_once __DIR__ . '/dal/TripDAL.php';

$trip_id    = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'];

if (!$trip_id) {
    die('Sefer id hatası');
}

// Güvenlik: sefer bu firmaya mı ait kontrol et
$tripDAL = new TripDAL();
$trip    = $tripDAL->idIleGetir($trip_id);

if (!$trip || $trip['company_id'] !== $company_id) {
    die("Yetkisiz işlem");
}

// ✅ DAL üzerinden SP çağrısı
$tripDAL->sil($trip_id);

header('Location: /company_panel.php');
exit();

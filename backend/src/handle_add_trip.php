<?php
require_once 'auth_check.php';
require_role('company');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('İzin verilmeyen metot.');
}

require_once __DIR__ . '/dal/TripDAL.php';

$company_id       = $_SESSION['company_id'];
$departure_city   = $_POST['departure_city'] ?? '';
$destination_city = $_POST['destination_city'] ?? '';
$departure_time   = $_POST['departure_time'] ?? '';
$arrival_time     = $_POST['arrival_time'] ?? '';
$price            = $_POST['price'] ?? '';
$capacity         = $_POST['capacity'] ?? '';

if (empty($departure_city) || empty($destination_city) ||
    empty($departure_time) || empty($arrival_time) ||
    empty($price) || empty($capacity)) {
    die('Lütfen tüm alanları doldurun.');
}

// ✅ DAL üzerinden SP çağrısı
$tripDAL = new TripDAL();
$trip_id = 'trip_' . uniqid();

$tripDAL->ekle(
    $trip_id, $company_id,
    $departure_city, $destination_city,
    $departure_time, $arrival_time,
    (float)$price, (int)$capacity
);

header('Location: /company_panel.php');
exit();

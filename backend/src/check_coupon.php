<?php
header('Content-Type: application/json');

require_once __DIR__ . '/dal/TripDAL.php';
require_once __DIR__ . '/dal/CouponDAL.php';

$coupon_code = trim($_GET['code'] ?? '');
$trip_id     = $_GET['trip_id'] ?? null;

if (empty($coupon_code) || empty($trip_id)) {
    echo json_encode(['valid' => false, 'message' => 'Kupon kodu ve sefer ID gereklidir.']);
    exit;
}

// ✅ DAL üzerinden SP çağrıları
$tripDAL   = new TripDAL();
$couponDAL = new CouponDAL();

$trip = $tripDAL->idIleGetir($trip_id);

if (!$trip) {
    echo json_encode(['valid' => false, 'message' => 'Sefer bulunamadı.']);
    exit;
}

$coupon = $couponDAL->kodIleGetir($coupon_code, $trip['company_id']);

if ($coupon) {
    echo json_encode([
        'valid'    => true,
        'discount' => (float)$coupon['discount'],
        'message'  => 'Kupon geçerli!'
    ]);
} else {
    echo json_encode([
        'valid'   => false,
        'message' => 'Geçersiz veya bu sefer için uygun olmayan kupon kodu.'
    ]);
}

<?php
require_once 'auth_check.php';
require_role('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Not allowed method");
}

// DAL katmanlarını dahil et
require_once __DIR__ . '/dal/TripDAL.php';
require_once __DIR__ . '/dal/UserDAL.php';
require_once __DIR__ . '/dal/TicketDAL.php';
require_once __DIR__ . '/dal/CouponDAL.php';

$trip_id           = $_POST['trip_id'] ?? null;
$selected_seats_str = $_POST['selected_seats'] ?? '';
$coupon_code       = trim($_POST['coupon_code'] ?? '');
$user_id           = $_SESSION['user_id'];

$selected_seats = !empty($selected_seats_str)
    ? explode(',', $selected_seats_str) : [];

if (empty($trip_id) || empty($selected_seats)) {
    die("Sefer id'si veya koltuk bilgisi eksik.");
}

// ✅ Tüm veri erişimi DAL üzerinden SP çağrısıyla yapılıyor
$tripDAL   = new TripDAL();
$userDAL   = new UserDAL();
$ticketDAL = new TicketDAL();
$couponDAL = new CouponDAL();

$trip = $tripDAL->idIleGetir($trip_id);
$user = $userDAL->idIleGetir($user_id);

if (!$trip || !$user) {
    die("Kullanıcı veya sefer bulunamadı.");
}

$seat_price      = (float)$trip['price'];
$user_balance    = (float)$user['balance'];
$number_of_seats = count($selected_seats);
$total_price     = $seat_price * $number_of_seats;
$trip_company_id = $trip['company_id'];
$applied_coupon  = null;

// Kupon kontrolü - SP üzerinden
if (!empty($coupon_code)) {
    $coupon = $couponDAL->kodIleGetir($coupon_code, $trip_company_id);
    if (!$coupon) {
        die("Geçersiz veya bu sefer için uygun olmayan kupon kodu girdiniz.");
    }
    // ✅ fn_indirimli_fiyat FUNCTION mantığı (SP içinde de tanımlı)
    $total_price    = round($total_price - ($total_price * $coupon['discount'] / 100), 2);
    $applied_coupon = $coupon;
}

if ($user_balance < $total_price) {
    die("Yetersiz bakiye.");
}

// Dolu koltuk kontrolü - SP üzerinden
$dolu_koltuklar = $ticketDAL->doluKoltuklariGetir($trip_id);
$cakisan = array_intersect(array_map('intval', $selected_seats), $dolu_koltuklar);
if (count($cakisan) > 0) {
    die("Şu koltuklar dolu: " . implode(', ', $cakisan));
}

try {
    // ✅ Bilet ekleme - SP üzerinden
    $ticket_id  = 'ticket_' . uniqid();
    $ticketDAL->ekle($ticket_id, $trip_id, $user_id, $total_price);

    // ✅ Koltuk ekleme - SP üzerinden
    foreach ($selected_seats as $seat) {
        $seat_id = 'seat_' . uniqid();
        $ticketDAL->koltukEkle($seat_id, $ticket_id, (int)$seat);
    }

    // ✅ Bakiye güncelleme - SP üzerinden
    $new_balance = $user_balance - $total_price;
    $userDAL->bakiyeGuncelle($user_id, $new_balance);

    // ✅ Kupon limiti düşür - SP üzerinden
    if ($applied_coupon) {
        $couponDAL->limitDusur($applied_coupon['id']);
    }

    $_SESSION['balance'] = $new_balance;

    header('Location: /my_tickets.php');
    exit();

} catch (Exception $e) {
    die("Bilet satın alma başarısız: " . $e->getMessage());
}

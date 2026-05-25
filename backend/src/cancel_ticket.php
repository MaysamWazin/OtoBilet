<?php
require_once 'auth_check.php';
require_role('user');

require_once __DIR__ . '/dal/TicketDAL.php';
require_once __DIR__ . '/dal/UserDAL.php';

$ticket_id = $_GET['id'] ?? null;
$user_id   = $_SESSION['user_id'];

if (!$ticket_id) {
    die("Bilet id'si bulunamadı");
}

// ✅ DAL üzerinden SP çağrısı
$ticketDAL = new TicketDAL();
$userDAL   = new UserDAL();

$ticket = $ticketDAL->idIleGetir($ticket_id);

if (!$ticket) {
    die("Bilet bulunamadı");
}

if ($ticket['user_id'] !== $user_id) {
    die("Yetkisiz erişim");
}

if ($ticket['status'] !== 'active') {
    die("Bu bilet zaten iptal edilmiş.");
}

$departure_time = new DateTime($ticket['departure_time']);
$now            = new DateTime();

if ($departure_time < $now) {
    die("Geçmiş bilet iptal edilemez.");
}

$interval              = $now->diff($departure_time);
$hours_until_departure = ($interval->days * 24) + $interval->h;

if ($hours_until_departure < 1) {
    die("Seferin kalkışına 1 saatten az kaldığı için bilet iptal edilemez.");
}

// ✅ Bileti iptal et - SP üzerinden (TRIGGER otomatik koltukları siler)
$ticketDAL->iptalEt($ticket_id);

// ✅ Bakiyeyi iade et - SP üzerinden
$user        = $userDAL->idIleGetir($user_id);
$new_balance = (float)$user['balance'] + (float)$ticket['total_price'];
$userDAL->bakiyeGuncelle($user_id, $new_balance);

$_SESSION['balance'] = $new_balance;

header('Location: /my_tickets.php');
exit();

<?php
require_once 'auth_check.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('İzin verilmeyen metot.');
}

require_once __DIR__ . '/dal/UserDAL.php';

$user_id    = $_POST['id'] ?? '';
$full_name  = $_POST['full_name'] ?? '';
$email      = $_POST['email'] ?? '';
$password   = $_POST['password'] ?? '';
$company_id = $_POST['company_id'] ?? '';

if (empty($user_id) || empty($full_name) || empty($email)) {
    die('Gerekli alanlar boş bırakılamaz.');
}

// ✅ DAL üzerinden SP çağrıları
$userDAL = new UserDAL();

// Ad, soyad, email güncelle
$userDAL->guncelle($user_id, $full_name, $email);

// Firma ataması varsa güncelle
if (!empty($company_id)) {
    $userDAL->companyAta($user_id, $company_id);
}

// Şifre değiştirme isteği varsa
if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    // Şifre güncellemesi için doğrudan DB sınıfını kullan (SP ile)
    require_once __DIR__ . '/db/Database.php';
    $pdo  = Database::getInstance()->getPDO();
    $stmt = $pdo->prepare("CALL sp_user_sifre_guncelle(?, ?)");
    $stmt->execute([$user_id, $hashed]);
}

header('Location: /admin_panel.php');
exit();

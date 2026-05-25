<?php
require_once 'auth_check.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Not Allowed Method");
}

require_once __DIR__ . '/dal/UserDAL.php';

$full_name  = $_POST['full_name'] ?? '';
$email      = $_POST['email'] ?? '';
$password   = $_POST['password'] ?? '';
$company_id = $_POST['company_id'] ?? '';

if (empty($full_name) || empty($email) || empty($password) || empty($company_id)) {
    die("Bütün alanları doldurmalısın");
}

// ✅ DAL üzerinden SP çağrısı
$userDAL = new UserDAL();

if ($userDAL->emailIleGetir($email)) {
    die("Bu email zaten kullanılıyor. <a href='/add_company_id.php'>Geri Dön</a>");
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$user_id         = 'user_' . uniqid();

$userDAL->ekle($user_id, $full_name, $email, 'company', $hashed_password);
$userDAL->companyAta($user_id, $company_id);

header('Location: /admin_panel.php');
exit();

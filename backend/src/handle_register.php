<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Not Allowed Method");
}

// DAL katmanını dahil et
require_once __DIR__ . '/dal/UserDAL.php';

$full_name = test_input($_POST['full_name'] ?? '');
$email     = test_input($_POST['email'] ?? '');
$password  = test_input($_POST['password'] ?? '');
$role      = 'user';

if (empty($full_name) || empty($email) || empty($password)) {
    die('Tüm alanları doldurunuz. <a href="/register.html">Geri Dön</a>');
}

if (strlen($password) < 6) {
    die('Şifre en az 6 karakter olmalıdır. <a href="/register.html">Geri Dön</a>');
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// ✅ DAL üzerinden SP çağrısı - direkt SQL yok
$userDAL = new UserDAL();

if ($userDAL->emailIleGetir($email)) {
    die('Bu e-posta adresi kullanılıyor. <a href="/register.html">Geri Dön</a>');
}

$id = uniqid('user_', true);
$userDAL->ekle($id, $full_name, $email, $role, $hashed_password);

echo 'Kayıt başarılı. <a href="/login.html">Giriş Yap</a>';

function test_input(string $data): string {
    return htmlspecialchars(stripslashes(trim($data)));
}

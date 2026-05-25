<?php
require_once 'auth_check.php';
require_role('admin');

require_once __DIR__ . '/dal/UserDAL.php';

$id = $_GET['id'] ?? '';

if (!$id) {
    die("Kullanıcı belirtilmedi");
}

// ✅ DAL üzerinden SP çağrısı
$userDAL = new UserDAL();
$userDAL->sil($id);

header('Location: /admin_panel.php');
exit();

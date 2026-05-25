<?php
require_once 'auth_check.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Not Allowed Method");
}

require_once __DIR__ . '/dal/CompanyDAL.php';

$company_name = $_POST['company_name'] ?? '';

if (empty($company_name)) {
    die("Firma adı bulunamadı");
}

// ✅ DAL üzerinden SP çağrısı
$companyDAL = new CompanyDAL();
$company_id = 'company_' . uniqid();

try {
    $companyDAL->ekle($company_id, $company_name);
    header('Location: /admin_panel.php');
    exit();
} catch (PDOException $e) {
    if ($e->getCode() == 23000 || $e->getCode() == '23000') {
        die("Böyle bir firma zaten mevcut. <a href='/add_company.php'>Geri Dön</a>");
    }
    die("Hata oluştu: " . $e->getMessage());
}

<?php
require_once 'auth_check.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Not Allowed Method");
}

require_once __DIR__ . '/dal/CompanyDAL.php';

$company_id   = $_POST['id'] ?? '';
$company_name = $_POST['company_name'] ?? '';

if (empty($company_id) || empty($company_name)) {
    die("Eksik Firma Bilgisi");
}

// ✅ DAL üzerinden SP çağrısı
$companyDAL = new CompanyDAL();
$companyDAL->guncelle($company_id, $company_name);

header('Location: /admin_panel.php');
exit();

<?php
require_once 'auth_check.php';
require_role('admin');

require_once __DIR__ . '/dal/CompanyDAL.php';

$company_id = $_GET['id'] ?? null;

if (!$company_id) {
    die('Firma id belirtilmedi');
}

// ✅ DAL üzerinden SP çağrısı
$companyDAL = new CompanyDAL();
$companyDAL->sil($company_id);

header('Location: /admin_panel.php');
exit();

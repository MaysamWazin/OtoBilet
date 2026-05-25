<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Not allowed method");
}

// DAL katmanını dahil et
require_once __DIR__ . '/dal/UserDAL.php';

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// ✅ DAL üzerinden SP çağrısı - direkt SQL yok
$userDAL = new UserDAL();
$user    = $userDAL->emailIleGetir($email);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['full_name']  = $user['full_name'];
    $_SESSION['role']       = $user['role'];
    $_SESSION['company_id'] = $user['company_id'];
    $_SESSION['balance']    = $user['balance'];

    header("Location: /index.php");
    exit();
} else {
    die('Email veya Parola hatalı <a href="/login.html">Tekrar Dene</a>');
}

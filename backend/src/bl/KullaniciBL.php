<?php
require_once __DIR__ . '/../dal/KullaniciDAL.php';

// ✅ BL: İş mantığı - DAL'ı kullanır, SQL yazmaz
class KullaniciBL {
    private KullaniciDAL $dal;

    public function __construct() {
        $this->dal = new KullaniciDAL();
    }

    public function kayitOl(string $ad, string $soyad, string $email, string $sifre): array {
        if (strlen($sifre) < 6)
            return ['basarili' => false, 'mesaj' => 'Şifre en az 6 karakter olmalı'];

        if ($this->dal->emailIleGetir($email))
            return ['basarili' => false, 'mesaj' => 'Bu e-posta zaten kayıtlı'];

        $hash = password_hash($sifre, PASSWORD_DEFAULT);
        $this->dal->ekle($ad, $soyad, $email, $hash, 'user');
        return ['basarili' => true, 'mesaj' => 'Kayıt başarılı'];
    }

    public function girisYap(string $email, string $sifre): array {
        $kullanici = $this->dal->emailIleGetir($email);
        if (!$kullanici || !password_verify($sifre, $kullanici['sifre']))
            return ['basarili' => false, 'mesaj' => 'E-posta veya şifre yanlış'];

        return ['basarili' => true, 'kullanici' => $kullanici];
    }
}
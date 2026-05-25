<?php
require_once __DIR__ . '/../dal/BiletDAL.php';
require_once __DIR__ . '/../dal/KuponDAL.php';
require_once __DIR__ . '/../dal/SeferDAL.php';
require_once __DIR__ . '/../dal/KullaniciDAL.php';

// ============================================================
// BL KATMANI - Bilet iş mantığı
// ============================================================
class BiletBL {
    private BiletDAL $biletDal;
    private KuponDAL $kuponDal;
    private SeferDAL $seferDal;
    private KullaniciDAL $kullaniciDal;

    public function __construct() {
        $this->biletDal    = new BiletDAL();
        $this->kuponDal    = new KuponDAL();
        $this->seferDal    = new SeferDAL();
        $this->kullaniciDal = new KullaniciDAL();
    }

    public function biletAl(int $kullaniciId, int $seferId,
                            int $koltukId, ?string $kuponKod): array {
        // Kullanıcı ve sefer bilgisini al
        $kullanicilar = $this->kullaniciDal->listele();
        $kullanici = null;
        foreach ($kullanicilar as $k) {
            if ($k['id'] === $kullaniciId) { $kullanici = $k; break; }
        }

        if (!$kullanici)
            return ['basarili' => false, 'mesaj' => 'Kullanıcı bulunamadı'];

        $seferler = $this->seferDal->hepsiniListele();
        $sefer = null;
        foreach ($seferler as $s) {
            if ($s['id'] === $seferId) { $sefer = $s; break; }
        }

        if (!$sefer)
            return ['basarili' => false, 'mesaj' => 'Sefer bulunamadı'];

        $fiyat   = (float)$sefer['fiyat'];
        $kuponId = null;

        // Kupon varsa uygula (FUNCTION kullanımı DAL üzerinden)
        if (!empty($kuponKod)) {
            $kupon = $this->kuponDal->kodIleGetir($kuponKod);
            if (!$kupon)
                return ['basarili' => false, 'mesaj' => 'Geçersiz veya süresi dolmuş kupon'];

            // fn_indirimli_fiyat SQL FUNCTION'ı DAL üzerinden çağır
            $fiyat   = $this->indirimliHesapla($fiyat, (float)$kupon['indirim_yuzdesi']);
            $kuponId = (int)$kupon['id'];
        }

        if ((float)$kullanici['bakiye'] < $fiyat)
            return ['basarili' => false, 'mesaj' => 'Yetersiz bakiye'];

        $this->biletDal->satinAl($kullaniciId, $seferId, $koltukId, $kuponId, $fiyat);
        return ['basarili' => true, 'mesaj' => 'Bilet başarıyla satın alındı', 'fiyat' => $fiyat];
    }

    public function biletIptal(int $biletId, int $kullaniciId): array {
        $bilet = $this->biletDal->getir($biletId);

        if (!$bilet || (int)$bilet['kullanici_id'] !== $kullaniciId)
            return ['basarili' => false, 'mesaj' => 'Bilet bulunamadı'];

        if ($bilet['durum'] === 'iptal')
            return ['basarili' => false, 'mesaj' => 'Bilet zaten iptal edilmiş'];

        // Seferden 1 saat öncesine kadar iptal
        $kalkis = strtotime($bilet['kalkis_saati']);
        if ($kalkis - time() < 3600)
            return ['basarili' => false, 'mesaj' => 'Seferden 1 saat önce iptal yapılamaz'];

        $this->biletDal->iptalEt($biletId, $kullaniciId);
        return ['basarili' => true, 'mesaj' => 'Bilet iptal edildi, bakiyeniz iade edildi'];
    }

    public function biletlerimGetir(int $kullaniciId): array {
        return $this->biletDal->kullaniciBiletleri($kullaniciId);
    }

    // PHP tarafında fn_indirimli_fiyat FUNCTION mantığı
    private function indirimliHesapla(float $fiyat, float $indirim): float {
        return round($fiyat - ($fiyat * $indirim / 100), 2);
    }
}

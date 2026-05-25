<?php
require_once __DIR__ . '/../db/Database.php';

// ============================================================
// DAL - Coupons Stored Procedure çağrıları
// ============================================================
class CouponDAL {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
    }

    public function ekle(string $id, string $code, float $discount,
                         ?string $companyId, int $limit, string $expire): bool {
        $stmt = $this->pdo->prepare("CALL sp_coupon_ekle(?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id, $code, $discount, $companyId, $limit, $expire]);
    }

    public function guncelle(string $id, int $limit, string $expire): bool {
        $stmt = $this->pdo->prepare("CALL sp_coupon_guncelle(?, ?, ?)");
        return $stmt->execute([$id, $limit, $expire]);
    }

    public function sil(string $id): bool {
        $stmt = $this->pdo->prepare("CALL sp_coupon_sil(?)");
        return $stmt->execute([$id]);
    }

    public function listele(): array {
        $stmt = $this->pdo->prepare("CALL sp_coupon_listele()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function kodIleGetir(string $code, string $companyId): ?array {
        $stmt = $this->pdo->prepare("CALL sp_coupon_kod_getir(?, ?)");
        $stmt->execute([$code, $companyId]);
        return $stmt->fetch() ?: null;
    }

    public function limitDusur(string $id): bool {
        $stmt = $this->pdo->prepare("CALL sp_coupon_limit_duşur(?)");
        return $stmt->execute([$id]);
    }
}

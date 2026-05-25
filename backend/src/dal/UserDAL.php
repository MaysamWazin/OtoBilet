<?php
require_once __DIR__ . '/../db/Database.php';

// ============================================================
// DAL - User Stored Procedure çağrıları
// ⚠️ SELECT/INSERT/UPDATE/DELETE YAZILMAZ - sadece CALL sp_...
// ============================================================
class UserDAL {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
    }

    public function ekle(string $id, string $fullName, string $email,
                         string $role, string $password): bool {
        $stmt = $this->pdo->prepare("CALL sp_user_ekle(?, ?, ?, ?, ?)");
        return $stmt->execute([$id, $fullName, $email, $role, $password]);
    }

    public function emailIleGetir(string $email): ?array {
        $stmt = $this->pdo->prepare("CALL sp_user_email_getir(?)");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function idIleGetir(string $id): ?array {
        $stmt = $this->pdo->prepare("CALL sp_user_id_getir(?)");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function guncelle(string $id, string $fullName, string $email): bool {
        $stmt = $this->pdo->prepare("CALL sp_user_guncelle(?, ?, ?)");
        return $stmt->execute([$id, $fullName, $email]);
    }

    public function sil(string $id): bool {
        $stmt = $this->pdo->prepare("CALL sp_user_sil(?)");
        return $stmt->execute([$id]);
    }

    public function listele(): array {
        $stmt = $this->pdo->prepare("CALL sp_user_listele()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function bakiyeGuncelle(string $id, float $bakiye): bool {
        $stmt = $this->pdo->prepare("CALL sp_user_bakiye_guncelle(?, ?)");
        return $stmt->execute([$id, $bakiye]);
    }

    public function companyAta(string $id, string $companyId): bool {
        $stmt = $this->pdo->prepare("CALL sp_user_company_ata(?, ?)");
        return $stmt->execute([$id, $companyId]);
    }
}

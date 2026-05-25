<?php
require_once __DIR__ . '/../db/Database.php';

// ============================================================
// DAL - Bus_Company Stored Procedure çağrıları
// ============================================================
class CompanyDAL {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
    }

    public function ekle(string $id, string $name, string $logo = ''): bool {
        $stmt = $this->pdo->prepare("CALL sp_company_ekle(?, ?, ?)");
        return $stmt->execute([$id, $name, $logo]);
    }

    public function guncelle(string $id, string $name, string $logo = ''): bool {
        $stmt = $this->pdo->prepare("CALL sp_company_guncelle(?, ?, ?)");
        return $stmt->execute([$id, $name, $logo]);
    }

    public function sil(string $id): bool {
        $stmt = $this->pdo->prepare("CALL sp_company_sil(?)");
        return $stmt->execute([$id]);
    }

    public function listele(): array {
        $stmt = $this->pdo->prepare("CALL sp_company_listele()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function idIleGetir(string $id): ?array {
        $stmt = $this->pdo->prepare("CALL sp_company_id_getir(?)");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}

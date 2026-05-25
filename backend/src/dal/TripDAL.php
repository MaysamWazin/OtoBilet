<?php
require_once __DIR__ . '/../db/Database.php';

// ============================================================
// DAL - Trips Stored Procedure çağrıları
// ============================================================
class TripDAL {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
    }

    public function ekle(string $id, string $companyId, string $departure,
                         string $destination, string $depTime, string $arrTime,
                         float $price, int $capacity): bool {
        $stmt = $this->pdo->prepare("CALL sp_trip_ekle(?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id, $companyId, $departure, $destination,
                               $depTime, $arrTime, $price, $capacity]);
    }

    public function guncelle(string $id, float $price, int $capacity): bool {
        $stmt = $this->pdo->prepare("CALL sp_trip_guncelle(?, ?, ?)");
        return $stmt->execute([$id, $price, $capacity]);
    }

    public function sil(string $id): bool {
        $stmt = $this->pdo->prepare("CALL sp_trip_sil(?)");
        return $stmt->execute([$id]);
    }

    public function listele(): array {
        $stmt = $this->pdo->prepare("CALL sp_trip_listele()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function ara(string $departure, string $destination): array {
        $stmt = $this->pdo->prepare("CALL sp_trip_ara(?, ?)");
        $stmt->execute([$departure, $destination]);
        return $stmt->fetchAll();
    }

    public function idIleGetir(string $id): ?array {
        $stmt = $this->pdo->prepare("CALL sp_trip_id_getir(?)");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function firmaListele(string $companyId): array {
        $stmt = $this->pdo->prepare("CALL sp_trip_firma_listele(?)");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }
}

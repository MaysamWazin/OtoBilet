<?php
require_once __DIR__ . '/../db/Database.php';

// ============================================================
// DAL - Tickets + Booked_Seats Stored Procedure çağrıları
// ============================================================
class TicketDAL {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
    }

    public function ekle(string $id, string $tripId,
                         string $userId, float $totalPrice): bool {
        $stmt = $this->pdo->prepare("CALL sp_ticket_ekle(?, ?, ?, ?)");
        return $stmt->execute([$id, $tripId, $userId, $totalPrice]);
    }

    public function iptalEt(string $id): bool {
        $stmt = $this->pdo->prepare("CALL sp_ticket_iptal(?)");
        return $stmt->execute([$id]);
    }

    public function kullaniciBiletleri(string $userId): array {
        $stmt = $this->pdo->prepare("CALL sp_ticket_listele_user(?)");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function idIleGetir(string $id): ?array {
        $stmt = $this->pdo->prepare("CALL sp_ticket_id_getir(?)");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function koltukEkle(string $id, string $ticketId, int $seatNumber): bool {
        $stmt = $this->pdo->prepare("CALL sp_seat_ekle(?, ?, ?)");
        return $stmt->execute([$id, $ticketId, $seatNumber]);
    }

    public function koltukSil(string $ticketId): bool {
        $stmt = $this->pdo->prepare("CALL sp_seat_sil_ticket(?)");
        return $stmt->execute([$ticketId]);
    }

    public function doluKoltuklariGetir(string $tripId): array {
        $stmt = $this->pdo->prepare("CALL sp_seat_dolu_getir(?)");
        $stmt->execute([$tripId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

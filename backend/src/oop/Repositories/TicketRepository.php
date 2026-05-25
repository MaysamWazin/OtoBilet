<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Database\DatabaseAdapter;

class TicketRepository implements RepositoryInterface
{
    private DatabaseAdapter $adapter;

    public function __construct(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findById(string $id): ?Ticket
    {
        $result = $this->adapter->query(
            "SELECT * FROM Tickets WHERE id = ?",
            [$id]
        );
        
        if (empty($result)) {
            return null;
        }
        
        $ticket = Ticket::fromArray($result[0]);
        
        // Booked seats'i yükle
        $seats = $this->getBookedSeatsByTicketId($id);
        $ticket->setBookedSeats($seats);
        
        return $ticket;
    }

    public function findByUserId(string $userId): array
    {
        $result = $this->adapter->query(
            "SELECT * FROM Tickets WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
        
        return array_map(function($row) {
            $ticket = Ticket::fromArray($row);
            $seats = $this->getBookedSeatsByTicketId($row['id']);
            $ticket->setBookedSeats($seats);
            return $ticket;
        }, $result);
    }

    public function findAll(): array
    {
        $result = $this->adapter->query("SELECT * FROM Tickets");
        return array_map(fn($row) => Ticket::fromArray($row), $result);
    }

    public function getBookedSeats(string $tripId): array
    {
        $result = $this->adapter->query(
            "SELECT BS.seat_number 
             FROM Booked_Seats BS 
             JOIN Tickets T ON BS.ticket_id = T.id 
             WHERE T.trip_id = ? AND T.status = 'active'",
            [$tripId]
        );
        
        return array_column($result, 'seat_number');
    }

    private function getBookedSeatsByTicketId(string $ticketId): array
    {
        $result = $this->adapter->query(
            "SELECT seat_number FROM Booked_Seats WHERE ticket_id = ?",
            [$ticketId]
        );
        
        return array_column($result, 'seat_number');
    }

    public function save(Ticket $ticket): bool
    {
        if ($ticket->getId() === null) {
            $ticket->setId(uniqid('ticket_', true));
        }

        $data = $ticket->toArray();
        $success = $this->adapter->execute(
            "INSERT INTO Tickets (id, trip_id, user_id, status, total_price, created_at) 
             VALUES (?, ?, ?, ?, ?, datetime('now'))",
            [
                $data['id'],
                $data['trip_id'],
                $data['user_id'],
                $data['status'],
                $data['total_price']
            ]
        );

        if ($success) {
            // Booked seats'i kaydet
            foreach ($ticket->getBookedSeats() as $seatNumber) {
                $this->adapter->execute(
                    "INSERT INTO Booked_Seats (id, ticket_id, seat_number, created_at) 
                     VALUES (?, ?, ?, datetime('now'))",
                    [uniqid('seat_', true), $ticket->getId(), $seatNumber]
                );
            }
        }

        return $success;
    }

    public function update(Ticket $ticket): bool
    {
        $data = $ticket->toArray();
        return $this->adapter->execute(
            "UPDATE Tickets SET trip_id = ?, user_id = ?, status = ?, total_price = ? WHERE id = ?",
            [
                $data['trip_id'],
                $data['user_id'],
                $data['status'],
                $data['total_price'],
                $data['id']
            ]
        );
    }

    public function delete(string $id): bool
    {
        // Önce booked seats'i sil
        $this->adapter->execute("DELETE FROM Booked_Seats WHERE ticket_id = ?", [$id]);
        // Sonra ticket'ı sil
        return $this->adapter->execute("DELETE FROM Tickets WHERE id = ?", [$id]);
    }
}


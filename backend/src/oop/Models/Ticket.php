<?php

namespace App\Models;

use App\States\TicketState;
use App\States\ActiveState;

/**
 * Ticket Model - State Pattern kullanır
 */
class Ticket
{
    private ?string $id;
    private string $tripId;
    private string $userId;
    private TicketState $state;
    private float $totalPrice;
    private array $bookedSeats;
    private string $createdAt;

    public function __construct(
        ?string $id = null,
        string $tripId = '',
        string $userId = '',
        ?TicketState $state = null,
        float $totalPrice = 0.0,
        array $bookedSeats = [],
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->tripId = $tripId;
        $this->userId = $userId;
        $this->state = $state ?? new ActiveState();
        $this->totalPrice = $totalPrice;
        $this->bookedSeats = $bookedSeats;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): ?string { return $this->id; }
    public function getTripId(): string { return $this->tripId; }
    public function getUserId(): string { return $this->userId; }
    public function getState(): TicketState { return $this->state; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function getBookedSeats(): array { return $this->bookedSeats; }
    public function getCreatedAt(): string { return $this->createdAt; }

    // Setters
    public function setId(string $id): void { $this->id = $id; }
    public function setTripId(string $tripId): void { $this->tripId = $tripId; }
    public function setUserId(string $userId): void { $this->userId = $userId; }
    public function setState(TicketState $state): void { $this->state = $state; }
    public function setTotalPrice(float $totalPrice): void { $this->totalPrice = $totalPrice; }
    public function setBookedSeats(array $bookedSeats): void { $this->bookedSeats = $bookedSeats; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    /**
     * State Pattern - Bilet iptal et
     */
    public function cancel(): void
    {
        $this->state->cancel($this);
    }

    /**
     * State Pattern - Bilet durumunu kontrol et
     */
    public function canCancel(): bool
    {
        return $this->state->canCancel();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->tripId,
            'user_id' => $this->userId,
            'status' => $this->state->getStatus(),
            'total_price' => $this->totalPrice,
            'created_at' => $this->createdAt
        ];
    }

    public static function fromArray(array $data): self
    {
        $state = TicketState::createFromStatus($data['status'] ?? 'active');
        return new self(
            $data['id'] ?? null,
            $data['trip_id'] ?? '',
            $data['user_id'] ?? '',
            $state,
            $data['total_price'] ?? 0.0,
            [],
            $data['created_at'] ?? ''
        );
    }
}


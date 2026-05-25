<?php

namespace App\Models;

/**
 * Trip Model - Entity Class
 */
class Trip
{
    private ?string $id;
    private string $companyId;
    private string $departureCity;
    private string $destinationCity;
    private string $departureTime;
    private string $arrivalTime;
    private float $price;
    private int $capacity;
    private string $createdAt;

    public function __construct(
        ?string $id = null,
        string $companyId = '',
        string $departureCity = '',
        string $destinationCity = '',
        string $departureTime = '',
        string $arrivalTime = '',
        float $price = 0.0,
        int $capacity = 0,
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->departureCity = $departureCity;
        $this->destinationCity = $destinationCity;
        $this->departureTime = $departureTime;
        $this->arrivalTime = $arrivalTime;
        $this->price = $price;
        $this->capacity = $capacity;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): ?string { return $this->id; }
    public function getCompanyId(): string { return $this->companyId; }
    public function getDepartureCity(): string { return $this->departureCity; }
    public function getDestinationCity(): string { return $this->destinationCity; }
    public function getDepartureTime(): string { return $this->departureTime; }
    public function getArrivalTime(): string { return $this->arrivalTime; }
    public function getPrice(): float { return $this->price; }
    public function getCapacity(): int { return $this->capacity; }
    public function getCreatedAt(): string { return $this->createdAt; }

    // Setters
    public function setId(string $id): void { $this->id = $id; }
    public function setCompanyId(string $companyId): void { $this->companyId = $companyId; }
    public function setDepartureCity(string $departureCity): void { $this->departureCity = $departureCity; }
    public function setDestinationCity(string $destinationCity): void { $this->destinationCity = $destinationCity; }
    public function setDepartureTime(string $departureTime): void { $this->departureTime = $departureTime; }
    public function setArrivalTime(string $arrivalTime): void { $this->arrivalTime = $arrivalTime; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function setCapacity(int $capacity): void { $this->capacity = $capacity; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->companyId,
            'departure_city' => $this->departureCity,
            'destination_city' => $this->destinationCity,
            'departure_time' => $this->departureTime,
            'arrival_time' => $this->arrivalTime,
            'price' => $this->price,
            'capacity' => $this->capacity,
            'created_at' => $this->createdAt
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['company_id'] ?? '',
            $data['departure_city'] ?? '',
            $data['destination_city'] ?? '',
            $data['departure_time'] ?? '',
            $data['arrival_time'] ?? '',
            $data['price'] ?? 0.0,
            $data['capacity'] ?? 0,
            $data['created_at'] ?? ''
        );
    }
}


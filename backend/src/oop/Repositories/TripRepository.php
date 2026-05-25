<?php

namespace App\Repositories;

use App\Models\Trip;
use App\Database\DatabaseAdapter;

/**
 * Trip Repository - Repository Pattern
 */
class TripRepository implements RepositoryInterface
{
    private DatabaseAdapter $adapter;

    public function __construct(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findById(string $id): ?Trip
    {
        $result = $this->adapter->query(
            "SELECT T.*, B.name as company_name 
             FROM Trips T 
             JOIN Bus_Company B ON T.company_id = B.id 
             WHERE T.id = ?",
            [$id]
        );
        
        if (empty($result)) {
            return null;
        }
        
        return Trip::fromArray($result[0]);
    }

    public function findAll(): array
    {
        $result = $this->adapter->query(
            "SELECT T.*, B.name as company_name 
             FROM Trips T 
             JOIN Bus_Company B ON T.company_id = B.id"
        );
        return array_map(fn($row) => Trip::fromArray($row), $result);
    }

    public function findByRoute(?string $departureCity = null, ?string $destinationCity = null): array
    {
        $sql = "SELECT T.*, B.name as company_name 
                FROM Trips T 
                JOIN Bus_Company B ON T.company_id = B.id 
                WHERE 1=1";
        $params = [];

        if (!empty($departureCity)) {
            $sql .= " AND T.departure_city LIKE ?";
            $params[] = '%' . $departureCity . '%';
        }

        if (!empty($destinationCity)) {
            $sql .= " AND T.destination_city LIKE ?";
            $params[] = '%' . $destinationCity . '%';
        }

        $result = $this->adapter->query($sql, $params);
        return array_map(fn($row) => Trip::fromArray($row), $result);
    }

    public function findByCompany(string $companyId): array
    {
        $result = $this->adapter->query(
            "SELECT * FROM Trips WHERE company_id = ? ORDER BY departure_time DESC",
            [$companyId]
        );
        return array_map(fn($row) => Trip::fromArray($row), $result);
    }

    public function save(Trip $trip): bool
    {
        if ($trip->getId() === null) {
            $trip->setId(uniqid('trip_', true));
        }

        $data = $trip->toArray();
        return $this->adapter->execute(
            "INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))",
            [
                $data['id'],
                $data['company_id'],
                $data['departure_city'],
                $data['destination_city'],
                $data['departure_time'],
                $data['arrival_time'],
                $data['price'],
                $data['capacity']
            ]
        );
    }

    public function update(Trip $trip): bool
    {
        $data = $trip->toArray();
        return $this->adapter->execute(
            "UPDATE Trips SET company_id = ?, departure_city = ?, destination_city = ?, departure_time = ?, arrival_time = ?, price = ?, capacity = ? WHERE id = ?",
            [
                $data['company_id'],
                $data['departure_city'],
                $data['destination_city'],
                $data['departure_time'],
                $data['arrival_time'],
                $data['price'],
                $data['capacity'],
                $data['id']
            ]
        );
    }

    public function delete(string $id): bool
    {
        return $this->adapter->execute("DELETE FROM Trips WHERE id = ?", [$id]);
    }
}


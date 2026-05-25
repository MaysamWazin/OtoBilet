<?php

namespace App\Repositories;

use App\Database\DatabaseAdapter;

class CompanyRepository implements RepositoryInterface
{
    private DatabaseAdapter $adapter;

    public function __construct(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findById(string $id): ?object
    {
        $result = $this->adapter->query(
            "SELECT * FROM Bus_Company WHERE id = ?",
            [$id]
        );
        
        return empty($result) ? null : (object)$result[0];
    }

    public function findAll(): array
    {
        return $this->adapter->query("SELECT * FROM Bus_Company");
    }

    public function save(object $entity): bool
    {
        $data = (array)$entity;
        if (!isset($data['id'])) {
            $data['id'] = uniqid('company_', true);
        }
        
        return $this->adapter->execute(
            "INSERT INTO Bus_Company (id, name, logo_path, created_at) 
             VALUES (?, ?, ?, datetime('now'))",
            [$data['id'], $data['name'] ?? '', $data['logo_path'] ?? null]
        );
    }

    public function update(object $entity): bool
    {
        $data = (array)$entity;
        return $this->adapter->execute(
            "UPDATE Bus_Company SET name = ?, logo_path = ? WHERE id = ?",
            [$data['name'] ?? '', $data['logo_path'] ?? null, $data['id']]
        );
    }

    public function delete(string $id): bool
    {
        return $this->adapter->execute("DELETE FROM Bus_Company WHERE id = ?", [$id]);
    }
}


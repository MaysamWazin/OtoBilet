<?php

namespace App\Repositories;

use App\Models\User;
use App\Database\DatabaseAdapter;

/**
 * User Repository - Repository Pattern
 */
class UserRepository implements RepositoryInterface
{
    private DatabaseAdapter $adapter;

    public function __construct(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findById(string $id): ?User
    {
        $result = $this->adapter->query(
            "SELECT * FROM User WHERE id = ?",
            [$id]
        );
        
        if (empty($result)) {
            return null;
        }
        
        return User::fromArray($result[0]);
    }

    public function findByEmail(string $email): ?User
    {
        $result = $this->adapter->query(
            "SELECT * FROM User WHERE email = ?",
            [$email]
        );
        
        if (empty($result)) {
            return null;
        }
        
        return User::fromArray($result[0]);
    }

    public function findAll(): array
    {
        $result = $this->adapter->query("SELECT * FROM User");
        return array_map(fn($row) => User::fromArray($row), $result);
    }

    public function save(User $user): bool
    {
        if ($user->getId() === null) {
            $user->setId(uniqid('user_', true));
        }

        $data = $user->toArray();
        return $this->adapter->execute(
            "INSERT INTO User (id, full_name, email, role, password, company_id, balance, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, datetime('now'))",
            [
                $data['id'],
                $data['full_name'],
                $data['email'],
                $data['role'],
                $data['password'],
                $data['company_id'],
                $data['balance']
            ]
        );
    }

    public function update(User $user): bool
    {
        $data = $user->toArray();
        return $this->adapter->execute(
            "UPDATE User SET full_name = ?, email = ?, role = ?, password = ?, company_id = ?, balance = ? WHERE id = ?",
            [
                $data['full_name'],
                $data['email'],
                $data['role'],
                $data['password'],
                $data['company_id'],
                $data['balance'],
                $data['id']
            ]
        );
    }

    public function delete(string $id): bool
    {
        return $this->adapter->execute("DELETE FROM User WHERE id = ?", [$id]);
    }
}


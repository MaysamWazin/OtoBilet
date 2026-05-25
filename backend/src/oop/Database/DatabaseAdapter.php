<?php

namespace App\Database;

use PDO;
use App\Database\SingletonDatabase;

/**
 * Adapter Pattern - Veritabanı Adapter
 * Farklı veritabanı türlerine uyum sağlar
 */
interface DatabaseAdapterInterface
{
    public function query(string $sql, array $params = []): array;
    public function execute(string $sql, array $params = []): bool;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollback(): bool;
}

/**
 * SQLite Adapter Implementation
 */
class DatabaseAdapter implements DatabaseAdapterInterface
{
    private PDO $connection;

    public function __construct()
    {
        $db = SingletonDatabase::getInstance();
        $this->connection = $db->getConnection();
    }

    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Query hatası: " . $e->getMessage());
        }
    }

    public function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            throw new \Exception("Execute hatası: " . $e->getMessage());
        }
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->rollback();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}


<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Singleton Pattern - Veritabanı Bağlantısı
 * Tek bir veritabanı instance'ı sağlar
 */
class SingletonDatabase
{
    private static ?SingletonDatabase $instance = null;
    private ?PDO $connection = null;
    private string $dbPath;

    /**
     * Private constructor - Singleton pattern
     */
    private function __construct()
    {
        $this->dbPath = __DIR__ . '/../../database/bilet_sistemi.sqlite';
        $this->connect();
    }

    /**
     * Singleton instance getter
     */
    public static function getInstance(): SingletonDatabase
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Veritabanı bağlantısı oluştur
     */
    private function connect(): void
    {
        try {
            $this->connection = new PDO('sqlite:' . $this->dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec('PRAGMA foreign_keys = ON;');
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }

    /**
     * PDO connection getter
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Clone metodunu engelle - Singleton pattern
     */
    private function __clone() {}

    /**
     * Unserialize metodunu engelle - Singleton pattern
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}


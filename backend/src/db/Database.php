<?php
// ============================================================
// DB KATMANI - MySQL Singleton Bağlantısı
// ============================================================
class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $host = getenv('DB_HOST') ?: 'db';
        $name = getenv('DB_NAME') ?: 'otobilet_db';
        $user = getenv('DB_USER') ?: 'otobilet_user';
        $pass = getenv('DB_PASS') ?: 'otobilet_pass';

        $this->pdo = new PDO(
            "mysql:host=$host;dbname=$name;charset=utf8mb4",
            $user, $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function getInstance(): self {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public function getPDO(): PDO {
        return $this->pdo;
    }
}

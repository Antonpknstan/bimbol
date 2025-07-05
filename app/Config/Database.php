<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Private constructor to prevent direct creation of object.
     */
    private function __construct() {}

    /**
     * Prevent cloning of the instance.
     */
    private function __clone() {}

    /**
     * Get the singleton PDO database connection.
     *
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'];
            $db   = $_ENV['DB_DATABASE'];
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Di lingkungan produksi, log error ini, jangan tampilkan ke user.
                // Untuk development, die() cukup untuk melihat error dengan jelas.
                die("Koneksi Database Gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
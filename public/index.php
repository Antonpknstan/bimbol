<?php

// Aktifkan error reporting untuk development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Muat Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Muat Variabel Lingkungan
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Error: Tidak dapat menemukan file .env. Pastikan Anda telah menyalin .env.example menjadi .env.");
}

use App\Config\Database;

try {
    $pdo = Database::getInstance();
    echo "<h1>Fase 2, Langkah 1 Berhasil!</h1>";
    echo "<p>Koneksi ke database '<strong>" . $_ENV['DB_DATABASE'] . "</strong>' berhasil dibuat.</p>";
    echo "<p>Versi Server MySQL: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "</p>";

} catch (PDOException $e) {
    echo "<h1>Fase 2, Langkah 1 GAGAL!</h1>";
    echo "<p style='color:red;'>Tidak dapat terhubung ke database: " . $e->getMessage() . "</p>";
    echo "<p><strong>Periksa kembali konfigurasi DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di file .env Anda. Pastikan database '" . $_ENV['DB_DATABASE'] . "' sudah dibuat di phpMyAdmin.</strong></p>";
}
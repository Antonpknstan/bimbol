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

// === Uji Keberhasilan Fase 1 ===
echo "<h1>Fase 1 Berhasil!</h1>";
echo "<p>Front Controller berhasil dimuat.</p>";

$dbHost = $_ENV['DB_HOST'] ?? 'Tidak Ditemukan';
echo "<p>Variabel .env berhasil dibaca: DB_HOST = <strong>" . htmlspecialchars($dbHost) . "</strong></p>";

// Placeholder untuk router akan ditambahkan di fase berikutnya
// ...
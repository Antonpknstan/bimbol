<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Error: Tidak dapat menemukan file .env.");
}

// 1. Muat definisi rute dari file terpisah
$dispatcher = require __DIR__ . '/../routes/web.php';

// 2. Ambil metode HTTP dan URI dari request
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Hapus query string (misal: "?foo=bar") dari URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// 3. Dispatch URI ke router
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 4. Proses hasil dari router
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // Rute tidak ditemukan
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        // Metode HTTP tidak diizinkan untuk rute ini
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo '<h1>405 Method Not Allowed</h1>';
        break;
    case FastRoute\Dispatcher::FOUND:
        // Rute ditemukan!
        $handler = $routeInfo[1]; // Handler: ['App\Controllers\HomeController', 'index']
        $vars = $routeInfo[2];    // Variabel dari URL (misal: /user/123)

        // Buat instance dari controller dan panggil metodenya
        [$class, $method] = $handler;

        try {
            // Buat objek controller
            $controller = new $class();
            // Panggil method di objek controller dengan variabel URL sebagai argumen
            call_user_func_array([$controller, $method], $vars);
        } catch (Exception $e) {
            http_response_code(500);
            echo '<h1>500 Internal Server Error</h1>';
            // Di lingkungan produksi, log error ini, jangan tampilkan ke user.
            if ($_ENV['APP_ENV'] === 'local') {
                echo '<pre>' . $e->getMessage() . '</pre>';
            }
        }
        break;
}
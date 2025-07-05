<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
App\Utils\Session::start();

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
        http_response_code(404);
        require __DIR__ . '/../app/Views/errors/404.php'; // Buat view error
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '<h1>405 Method Not Allowed</h1>';
        break;
    // public/index.php
case FastRoute\Dispatcher::FOUND:
    $routeData = $routeInfo[1];
    $vars = $routeInfo[2];
    
    // --- Logika Middleware BARU ---
    // Cek jika rute ini memiliki middleware yang ditentukan
    if (isset($routeData['middleware'])) {
        $permission = $routeData['middleware'];
        // Jalankan pemeriksaan izin
        \App\Middleware\PermissionMiddleware::check($permission);
    }

    // --- Logika Middleware Lama (untuk Guest/Auth non-admin) ---
    $uri = $_SERVER['REQUEST_URI'];
    if ($uri === '/login' || $uri === '/register') {
        \App\Middleware\AuthMiddleware::handleGuest();
    }
    // Proteksi dashboard user biasa (jika diperlukan)
    if ($uri === '/dashboard') {
        \App\Middleware\AuthMiddleware::handleAuth();
    }
    // --- Akhir Logika Middleware ---

    $handler = $routeData['handler'];
    [$class, $method] = $handler;

    try {
        $controller = new $class();
        call_user_func_array([$controller, $method], $vars);
    } catch (Exception $e) {
        // ... (error handling yang sudah ada)
    }
    break;
}
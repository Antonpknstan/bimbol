<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
App\Utils\Session::start();
\App\Middleware\CSRFMiddleware::handle();

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
        // Pastikan Anda sudah membuat file ini
        require __DIR__ . '/../app/Views/errors/404.php';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '<h1>405 Method Not Allowed</h1>';
        break;
    case FastRoute\Dispatcher::FOUND:
        $routeData = $routeInfo[1];
        $vars = $routeInfo[2];
        
        $handler = null;
        $permission = null;

        // --- Logika Cerdas untuk Menangani Dua Format Rute ---
        // Cek jika route didefinisikan dengan format baru (array asosiatif)
        if (is_array($routeData) && isset($routeData['handler'])) {
            $handler = $routeData['handler'];
            $permission = $routeData['middleware'] ?? null;
        } else {
            // Jika tidak, asumsikan ini format lama (array biasa)
            $handler = $routeData;
        }

        // --- Validasi Handler ---
        if (!is_array($handler) || count($handler) !== 2) {
            http_response_code(500);
            echo '<h1>500 Internal Server Error - Konfigurasi Rute Tidak Valid.</h1>';
            // Log this error for debugging
            error_log("Invalid route handler configuration for URI: " . $uri);
            exit;
        }

        // --- Eksekusi Middleware ---
        // 1. Middleware Izin (RBAC)
        if ($permission) {
            \App\Middleware\PermissionMiddleware::check($permission);
        }
        
        // 2. Middleware Sesi (Guest & Auth)
        $uri = $_SERVER['REQUEST_URI'];
        // Rute yang hanya bisa diakses T tamu (belum login)
        if (in_array($uri, ['/login', '/register'])) {
            \App\Middleware\AuthMiddleware::handleGuest();
        }
        // Rute yang butuh login (selain admin)
        $protectedUserRoutes = ['/dashboard', '/logout', '/purchases/history']; // Tambahkan rute lain di sini
        foreach ($protectedUserRoutes as $protectedRoute) {
            if (strpos($uri, $protectedRoute) === 0) {
                \App\Middleware\AuthMiddleware::handleAuth();
                break;
            }
        }
        
        // --- Eksekusi Controller ---
        [$class, $method] = $handler;

        try {
            if (!class_exists($class)) {
                throw new Exception("Controller class '$class' not found.");
            }
            $controller = new $class();
            if (!method_exists($controller, $method)) {
                throw new Exception("Method '$method' not found in controller '$class'.");
            }
            call_user_func_array([$controller, $method], $vars);
        } catch (Throwable $e) { // Gunakan Throwable untuk menangkap Error dan Exception
            http_response_code(500);
            echo '<h1>500 Internal Server Error</h1>';
            // Di lingkungan produksi, log error ini, jangan tampilkan ke user.
            if ($_ENV['APP_ENV'] === 'local') {
                echo '<pre>Error: ' . htmlspecialchars($e->getMessage()) . '</pre>';
                echo '<pre>File: ' . $e->getFile() . ' on line ' . $e->getLine() . '</pre>';
                echo '<pre>Stack Trace: <br>' . nl2br(htmlspecialchars($e->getTraceAsString())) . '</pre>';
            }
            error_log($e);
        }
        break;
}
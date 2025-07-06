<?php
namespace App\Middleware;

use App\Utils\CSRF;

class CSRFMiddleware
{
    public static function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = $_POST['_csrf_token'] ?? '';
            if (!CSRF::validateToken($submittedToken)) {
                // Token tidak valid atau tidak ada
                http_response_code(419); // "Authentication Timeout" atau "CSRF Token Mismatch"
                die('Sesi tidak valid atau telah kedaluwarsa. Silakan kembali dan coba lagi.');
            }
        }
    }
}
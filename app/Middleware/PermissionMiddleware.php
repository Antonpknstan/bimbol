<?php
namespace App\Middleware;

use App\Utils\Session;
use App\Models\User;

class PermissionMiddleware
{
    /**
     * Memeriksa apakah pengguna yang login memiliki izin yang diperlukan.
     * @param string $permission Nama izin yang diperlukan (e.g., 'manage_users')
     */
    public static function check(string $permission)
    {
        // 1. Pastikan user sudah login
        if (!Session::has('user')) {
            header('Location: /login');
            exit;
        }
        
        // 2. Cek izin
        $userId = Session::get('user')['id'];
        $userModel = new User();

        if (!$userModel->hasPermission($userId, $permission)) {
            // User tidak memiliki izin, redirect atau tampilkan halaman error
            http_response_code(403); // Forbidden
            // Untuk pengalaman pengguna yang lebih baik, tampilkan halaman error kustom
            // require __DIR__ . '/../app/Views/errors/403.php'; 
            echo '<h1>403 Forbidden - Anda tidak memiliki izin untuk mengakses halaman ini.</h1>';
            exit;
        }
    }
}
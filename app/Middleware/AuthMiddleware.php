<?php
namespace App\Middleware;

use App\Utils\Session;

class AuthMiddleware
{
    /**
     * Untuk halaman yang hanya boleh diakses oleh user yang sudah login.
     * Jika belum login, redirect ke halaman login.
     */
    public static function handleAuth() {
        if (!Session::has('user')) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Untuk halaman yang hanya boleh diakses oleh tamu (belum login).
     * Jika sudah login, redirect ke dashboard.
     */
    public static function handleGuest() {
        if (Session::has('user')) {
            header('Location: /dashboard');
            exit;
        }
    }
}
<?php
namespace App\Utils;

class CSRF
{
    private static string $tokenName = '_csrf_token';

    /**
     * Menghasilkan token CSRF baru atau mengembalikan yang sudah ada di sesi.
     */
    public static function generateToken(): string
    {
        if (empty(Session::get(self::$tokenName))) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::$tokenName, $token);
        }
        return Session::get(self::$tokenName);
    }

    /**
     * Memvalidasi token yang dikirimkan dengan yang ada di sesi.
     */
    public static function validateToken(string $submittedToken): bool
    {
        $token = Session::get(self::$tokenName);
        return !empty($token) && hash_equals($token, $submittedToken);
    }

    /**
     * Menghasilkan input field HTML yang tersembunyi berisi token.
     */
    public static function field(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . $token . '">';
    }
}
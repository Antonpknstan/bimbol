<?php

namespace App\Controllers;

abstract class BaseController
{
    /**
     * Render sebuah file view dan bungkus dengan layout utama.
     *
     * @param string $viewPath Path ke file view (misal: 'home' atau 'auth/login')
     * @param array $data Data untuk diekstrak menjadi variabel di dalam view dan layout
     * @param string $layoutPath Path ke file layout utama
     */
    protected function render(string $viewPath, array $data = [], string $layoutPath = 'layout/app')
    {
        // Path lengkap ke file view konten
        $fullViewPath = __DIR__ . '/../Views/' . $viewPath . '.php';

        if (!file_exists($fullViewPath)) {
            http_response_code(500);
            echo "Error: View file not found at: " . htmlspecialchars($fullViewPath);
            exit;
        }

        // Ekstrak data agar bisa diakses sebagai variabel di view dan layout
        extract($data);

        // --- Langkah A: Render konten spesifik halaman ke dalam sebuah variabel ---
        ob_start();
        require $fullViewPath;
        $content = ob_get_clean(); // $content sekarang berisi HTML dari home.php

        // --- Langkah B: Render layout utama, yang akan menggunakan variabel $content ---
        $fullLayoutPath = __DIR__ . '/../Views/' . $layoutPath . '.php';

        if (!file_exists($fullLayoutPath)) {
            http_response_code(500);
            echo "Error: Layout file not found at: " . htmlspecialchars($fullLayoutPath);
            exit;
        }

        // Tampilkan layout utama, yang di dalamnya akan menampilkan $content
        require $fullLayoutPath;
    }

    /**
     * Redirect ke URL lain.
     */
        /**
     * Redirect ke URL lain dengan lebih andal.
     */
    protected function redirect(string $url)
    {
        // Jika ada output buffer yang aktif, bersihkan dulu.
        // Ini mencegah error "headers already sent" dalam beberapa kasus.
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Gunakan APP_URL dari .env, atau default ke URL server saat ini jika tidak ada.
        $baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://' . $_SERVER['HTTP_HOST'], '/');
        $location = $baseUrl . $url;

        // Kirim header redirect dengan status code 302 (Found) secara eksplisit.
        header('Location: ' . $location, true, 302);
        
        // Pastikan tidak ada kode lain yang dieksekusi setelah redirect.
        exit;
    }

    /**
     * Mengembalikan response JSON.
     */
    protected function json(array $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
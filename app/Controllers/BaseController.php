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
    protected function redirect(string $url)
    {
        // Pastikan APP_URL di .env diakhiri tanpa slash
        $baseUrl = rtrim($_ENV['APP_URL'], '/');
        header('Location: ' . $baseUrl . $url);
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
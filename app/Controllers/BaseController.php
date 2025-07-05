<?php

namespace App\Controllers;

abstract class BaseController
{
    /**
     * Render sebuah file view dengan data yang diberikan.
     *
     * @param string $viewPath Path ke file view (misal: 'home' atau 'auth/login')
     * @param array $data Data untuk diekstrak menjadi variabel di dalam view
     */
    protected function render(string $viewPath, array $data = [])
    {
        // Ubah path seperti 'auth/login' menjadi path file sistem seperti 'app/Views/auth/login.php'
        $fullViewPath = __DIR__ . '/../Views/' . $viewPath . '.php';

        if (!file_exists($fullViewPath)) {
            // Tampilkan error jika file view tidak ditemukan
            http_response_code(500);
            echo "Error: View file not found at: " . htmlspecialchars($fullViewPath);
            exit;
        }

        // Ekstrak array $data menjadi variabel-variabel individual.
        // Contoh: ['title' => 'My Page'] akan menjadi variabel $title = 'My Page'
        extract($data);

        // Mulai output buffering untuk menangkap output dari view
        ob_start();

        // Sertakan file view, yang sekarang memiliki akses ke variabel yang diekstrak
        require $fullViewPath;

        // Ambil konten buffer dan bersihkan
        $content = ob_get_clean();

        // Tampilkan kontennya (nantinya bisa dimasukkan ke dalam layout utama)
        echo $content;
    }

    /**
     * Redirect ke URL lain.
     */
    protected function redirect(string $url)
    {
        header('Location: ' . $url);
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
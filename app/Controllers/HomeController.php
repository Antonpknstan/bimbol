<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    /**
     * Menampilkan halaman beranda.
     */
    public function index()
    {
        // Data yang akan dikirim ke view
        $data = [
            'title' => 'Selamat Datang di Bimbel Online!',
            'description' => 'Platform belajar online terbaik untuk persiapan masa depan Anda.'
        ];

        // Render view 'home.php' dan kirimkan data ke dalamnya
        $this->render('home', $data);
    }
}
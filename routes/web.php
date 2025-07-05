<?php

// Menggunakan library FastRoute yang sudah kita install
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// 'simpleDispatcher' membuat objek dispatcher yang akan kita gunakan di index.php
// Fungsi ini mengambil sebuah callback yang mendefinisikan semua rute aplikasi.
return simpleDispatcher(function (RouteCollector $r) {
    // Rute untuk Halaman Beranda (Homepage)
    // Method: GET
    // URL: /
    // Handler: Kelas HomeController, method index()
    $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);

    // Rute untuk Halaman Login (Contoh untuk nanti)
    // $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLoginForm']);
    // $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);

    // Anda akan menambahkan semua rute lain di sini...
});
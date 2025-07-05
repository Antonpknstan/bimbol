<?php

// Menggunakan library FastRoute yang sudah kita install
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);

    // Rute Autentikasi
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'showRegisterForm']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);

    $r->addRoute('GET', '/packages', ['App\Controllers\PackageController', 'index']);

    $r->addRoute('POST', '/purchase/{packageId:\d+}', ['App\Controllers\PurchaseController', 'buy']);
    $r->addRoute('GET', '/purchases/history', ['App\Controllers\PurchaseController', 'history']);

    // Rute Konten Pembelajaran
    $r->addRoute('GET', '/courses', ['App\Controllers\LearningController', 'index']);
    // \d+ memastikan {id} hanya menerima angka (digit)
    $r->addRoute('GET', '/course/{id:\d+}', ['App\Controllers\LearningController', 'show']);
    
    // Rute Dashboard (dilindungi)
    $r->addRoute('GET', '/dashboard', ['App\Controllers\DashboardController', 'index']);
});
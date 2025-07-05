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

    // Rute Asesmen & Upaya
    $r->addRoute('GET', '/assessment/start/{id:\d+}', ['App\Controllers\AssessmentController', 'start']);
    $r->addRoute('GET', '/assessment/attempt/{id:\d+}', ['App\Controllers\AssessmentController', 'showAttempt']);
    $r->addRoute('POST', '/assessment/submit/{id:\d+}', ['App\Controllers\AssessmentController', 'submit']);
    $r->addRoute('GET', '/assessment/result/{id:\d+}', ['App\Controllers\AssessmentController', 'showResult']);

    // Rute Progres
    $r->addRoute('POST', '/progress/mark-lesson-complete/{id:\d+}', ['App\Controllers\ProgressController', 'markLessonAsCompleted']);

    // Rute Pelajaran
    $r->addRoute('GET', '/lesson/{id:\d+}', ['App\Controllers\LessonController', 'show']);

    // Rute Tryout
$r->addRoute('GET', '/tryouts', ['App\Controllers\TryoutController', 'index']);
$r->addRoute('GET', '/tryout/leaderboard/{id:\d+}', ['App\Controllers\TryoutController', 'showLeaderboard']);

// --- RUTE ADMIN ---
$r->addRoute('GET', '/admin/dashboard', [
    'handler' => ['App\Controllers\Admin\DashboardController', 'index'], // 
    'middleware' => 'view_admin_dashboard'
]);
$r->addRoute('GET', '/admin/upload/questions', [
    'handler' => ['App\Controllers\Admin\UploadController', 'showQuestionUploadForm'],
    'middleware' => 'upload_questions_batch'
]);
$r->addRoute('POST', '/admin/upload/questions', [
    'handler' => ['App\Controllers\Admin\UploadController', 'handleQuestionUpload'],
    'middleware' => 'upload_questions_batch'
]);
    
    // Rute Dashboard (dilindungi)
    $r->addRoute('GET', '/dashboard', ['App\Controllers\DashboardController', 'index']);
});
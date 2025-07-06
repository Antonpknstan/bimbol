<?php

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    // Setiap rute sekarang menggunakan struktur ['handler' => [...]]
    
    // Rute Publik
    $r->addRoute('GET', '/', ['handler' => ['App\Controllers\HomeController', 'index']]);

    // Rute Autentikasi (Tamu)
    $r->addRoute('GET', '/login', ['handler' => ['App\Controllers\AuthController', 'showLoginForm']]);
    $r->addRoute('POST', '/login', ['handler' => ['App\Controllers\AuthController', 'login']]);
    $r->addRoute('GET', '/register', ['handler' => ['App\Controllers\AuthController', 'showRegisterForm']]);
    $r->addRoute('POST', '/register', ['handler' => ['App\Controllers\AuthController', 'register']]);
    
    // Rute yang membutuhkan login
    $r->addRoute('GET', '/logout', ['handler' => ['App\Controllers\AuthController', 'logout']]);
    $r->addRoute('GET', '/dashboard', ['handler' => ['App\Controllers\DashboardController', 'index']]);

    // Rute Konten Pembelajaran
    $r->addRoute('GET', '/courses', ['handler' => ['App\Controllers\LearningController', 'index']]);
    $r->addRoute('GET', '/course/{id:\d+}', ['handler' => ['App\Controllers\LearningController', 'show']]);
    $r->addRoute('GET', '/lesson/{id:\d+}', ['handler' => ['App\Controllers\LessonController', 'show']]);

    // Rute Paket
    $r->addRoute('GET', '/packages', ['handler' => ['App\Controllers\PackageController', 'index']]);

    // Rute Pembelian
    $r->addRoute('POST', '/purchase/{id:\d+}', ['handler' => ['App\Controllers\PurchaseController', 'buy']]);
    $r->addRoute('GET', '/purchases/history', ['handler' => ['App\Controllers\PurchaseController', 'history']]);

    // Rute Asesmen & Upaya
    $r->addRoute('GET', '/assessment/start/{id:\d+}', ['handler' => ['App\Controllers\AssessmentController', 'start']]);
    $r->addRoute('GET', '/assessment/attempt/{id:\d+}', ['handler' => ['App\Controllers\AssessmentController', 'showAttempt']]);
    $r->addRoute('POST', '/assessment/submit/{id:\d+}', ['handler' => ['App\Controllers\AssessmentController', 'submit']]);
    $r->addRoute('GET', '/assessment/result/{id:\d+}', ['handler' => ['App\Controllers\AssessmentController', 'showResult']]);

    // Rute Progres
    $r->addRoute('POST', '/progress/mark-lesson-complete/{id:\d+}', ['handler' => ['App\Controllers\ProgressController', 'markLessonAsCompleted']]);
    
    // Rute Tryout
    $r->addRoute('GET', '/tryouts', ['handler' => ['App\Controllers\TryoutController', 'index']]);
    $r->addRoute('GET', '/tryout/leaderboard/{id:\d+}', ['handler' => ['App\Controllers\TryoutController', 'showLeaderboard']]);

    // Rute Laporan (API Internal)
$r->addRoute('POST', '/report/submit', ['handler' => ['App\Controllers\ReportController', 'submit']]);

// Rute Notifikasi (API Internal)
$r->addRoute('GET', '/notifications/fetch', ['handler' => ['App\Controllers\NotificationController', 'fetch']]);
$r->addRoute('POST', '/notifications/mark-read', ['handler' => ['App\Controllers\NotificationController', 'markRead']]);

// Rute Admin untuk Laporan
$r->addRoute('GET', '/admin/reports', [
    'handler' => ['App\Controllers\Admin\ReportManagementController', 'listReports'],
    'middleware' => 'view_reports'
]);
$r->addRoute('POST', '/admin/reports/{id:\d+}/status', [
    'handler' => ['App\Controllers\Admin\ReportManagementController', 'updateStatus'],
    'middleware' => 'view_reports'
]);

// Rute Reset Password
$r->addRoute('GET', '/forgot-password', ['handler' => ['App\Controllers\AuthController', 'showForgotPasswordForm']]);
$r->addRoute('POST', '/forgot-password', ['handler' => ['App\Controllers\AuthController', 'sendResetLink']]);
$r->addRoute('POST', '/reset-password', ['handler' => ['App\Controllers\AuthController', 'processPasswordReset']]);
// Regex [0-9a-zA-Z]+ memastikan token hanya terdiri dari huruf dan angka.
$r->addRoute('GET', '/reset-password/{token:[0-9a-zA-Z]+}', ['handler' => ['App\Controllers\AuthController', 'showResetPasswordForm']]);
// Terapkan juga untuk rute verifikasi email
$r->addRoute('GET', '/verify-email/{token:[0-9a-zA-Z]+}', ['handler' => ['App\Controllers\AuthController', 'verifyEmail']]);

    // --- RUTE ADMIN (Dengan Middleware) ---
    $r->addRoute('GET', '/admin/dashboard', [
        'handler' => ['App\Controllers\Admin\DashboardController', 'index'],
        'middleware' => 'view_admin_dashboard'
    ]);
    $r->addRoute('GET', '/admin/upload/questions', [
        'handler' => ['App\Controllers\Admin\UploadController', 'showUploadForm'],
        'middleware' => 'upload_questions_batch'
    ]);
    $r->addRoute('POST', '/admin/upload/excel', [ // Ganti nama rute ini
        'handler' => ['App\Controllers\Admin\UploadController', 'handleExcelUpload'],
        'middleware' => 'upload_questions_batch'
    ]);
    $r->addRoute('GET', '/admin/upload/review/{id:\d+}', [
        'handler' => ['App\Controllers\Admin\UploadController', 'showReviewPage'],
        'middleware' => 'upload_questions_batch'
    ]);
    $r->addRoute('POST', '/admin/upload/zip/{id:\d+}', [
        'handler' => ['App\Controllers\Admin\UploadController', 'handleZipUpload'],
        'middleware' => 'upload_questions_batch'
    ]);
    $r->addRoute('POST', '/admin/upload/finalize/{id:\d+}', [
        'handler' => ['App\Controllers\Admin\UploadController', 'finalize'],
        'middleware' => 'upload_questions_batch'
    ]);
    $r->addRoute('GET', '/admin/users', [
        'handler' => ['App\Controllers\Admin\UserManagementController', 'listUsers'],
        'middleware' => 'manage_users'
    ]);
    $r->addRoute('GET', '/admin/users/{id:\d+}/roles', [
        'handler' => ['App\Controllers\Admin\UserManagementController', 'editUserRoles'],
        'middleware' => 'assign_roles'
    ]);
    $r->addRoute('POST', '/admin/users/{id:\d+}/roles', [
        'handler' => ['App\Controllers\Admin\UserManagementController', 'updateUserRoles'],
        'middleware' => 'assign_roles'
    ]);
});
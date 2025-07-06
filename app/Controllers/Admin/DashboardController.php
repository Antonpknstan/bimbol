<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Course;
use App\Models\Purchase;

class DashboardController extends BaseController
{
    public function index()
    {
        // Siapkan model-model yang dibutuhkan
        $userModel = new User();
        $courseModel = new Course();
        $purchaseModel = new Purchase();

        // Ambil data statistik dari model
        // (Kita perlu membuat method-method ini di model yang bersangkutan)
        $totalUsers = $userModel->countAll();
        $totalCourses = $courseModel->countAll();
        $totalSuccessPurchases = $purchaseModel->countByStatus('success');
        $recentUsers = $userModel->findRecent(5); // Ambil 5 user terbaru

        // Data untuk dikirim ke view
        $data = [
            'title' => 'Dashboard Admin',
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses,
            'totalSuccessPurchases' => $totalSuccessPurchases,
            'recentUsers' => $recentUsers
        ];

        // Render view dashboard admin
        $this->render('admin/dashboard/index', $data, 'layout/admin');
    }
}
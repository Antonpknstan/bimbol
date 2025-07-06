<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Report;
use App\Utils\Session;

class ReportManagementController extends BaseController
{
    public function listReports()
    {
        $reportModel = new Report();
        $reports = $reportModel->findAllWithDetails();

        $this->render('admin/reports/list', [
            'title' => 'Manajemen Laporan Masalah',
            'reports' => $reports
        ], 'layout/admin');
    }
    
    public function updateStatus(int $reportId)
    {
        $status = $_POST['status'] ?? null;
        if (!$status) {
            $this->redirect('/admin/reports'); // Atau tampilkan error
            return;
        }
        
        $reportModel = new Report();
        $reportModel->updateStatus($reportId, $status, Session::get('user')['id']);
        
        // Opsional: Kirim notifikasi ke user bahwa laporannya sudah ditangani
        
        $this->redirect('/admin/reports');
    }
}
<?php
namespace App\Controllers\Admin; // Pastikan namespace ini benar

use App\Controllers\BaseController;
use App\Models\Report;
use App\Utils\Session;

class ReportManagementController extends BaseController
{
    /**
     * Menampilkan daftar semua laporan masalah.
     */
    public function listReports()
    {
        $reportModel = new Report();
        $reports = $reportModel->findAllWithDetails();

        $this->render('admin/reports/list', [
            'title' => 'Manajemen Laporan Masalah',
            'reports' => $reports
        ], 'layout/admin');
    }
    
    /**
     * Memperbarui status laporan.
     * @param int $id ID laporan dari URL
     */
    public function updateStatus(int $id)
    {
        $status = $_POST['status'] ?? null;
        if (!$status || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/reports');
            return;
        }
        
        $reportModel = new Report();
        $success = $reportModel->updateStatus($id, $status, Session::get('user')['id']);
        
        if ($success) {
            // Opsional: Kirim notifikasi ke user bahwa laporannya sudah ditangani
            Session::set('flash_message', ['type' => 'success', 'message' => 'Status laporan berhasil diperbarui.']);
        } else {
            Session::set('flash_message', ['type' => 'error', 'message' => 'Gagal memperbarui status laporan.']);
        }
        
        $this->redirect('/admin/reports');
    }
}
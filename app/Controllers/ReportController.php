<?php
namespace App\Controllers;

use App\Models\Report;
use App\Utils\Session;

class ReportController extends BaseController
{
    /**
     * Menangani pengiriman formulir laporan.
     * Endpoint ini akan menjadi API internal yang dipanggil oleh JavaScript.
     */
    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::has('user')) {
            return $this->json(['status' => 'error', 'message' => 'Invalid request.'], 400);
        }
        
        $reportType = $_POST['report_type'] ?? null;
        $itemId = $_POST['item_id'] ?? null;
        $description = $_POST['description'] ?? null;

        if (empty($reportType) || empty($itemId) || empty($description)) {
            return $this->json(['status' => 'error', 'message' => 'Semua field harus diisi.'], 400);
        }

        $reportModel = new Report();
        $success = $reportModel->create(Session::get('user')['id'], $reportType, (int)$itemId, $description);

        if ($success) {
            return $this->json(['status' => 'success', 'message' => 'Laporan Anda telah berhasil dikirim. Terima kasih!']);
        } else {
            return $this->json(['status' => 'error', 'message' => 'Gagal mengirim laporan.'], 500);
        }
    }
}
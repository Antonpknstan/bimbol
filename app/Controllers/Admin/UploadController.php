<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\BatchProcessingService;
use App\Utils\Session;
use App\Models\QuestionUploadBatch;
use App\Models\StagingQuestion;

class UploadController extends BaseController
{
    private BatchProcessingService $batchService;
    private QuestionUploadBatch $batchModel;
    private StagingQuestion $stagingModel;

    public function __construct()
    {
        $this->batchService = new BatchProcessingService();
        $this->batchModel = new QuestionUploadBatch();
        $this->stagingModel = new StagingQuestion();
    }

    // Menampilkan form upload Excel
    public function showUploadForm()
    {
        $flashMessage = Session::get('flash_message');
        Session::set('flash_message', null); // Hapus setelah dibaca

        $this->render('admin/uploads/form', [
            'title' => 'Upload Pertanyaan Batch (Tahap 1)',
            'flash_message' => $flashMessage
        ], 'layout/admin');
    }

    // Memproses Excel
    public function handleExcelUpload()
    {
        // Validasi request yang lebih ketat
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            Session::set('flash_message', ['type' => 'error', 'message' => 'Upload gagal. Pastikan Anda telah memilih file yang valid.']);
            $this->redirect('/admin/upload/questions');
            return;
        }

        $result = $this->batchService->stageExcelUpload($_FILES['excel_file'], Session::get('user')['id']);

        if (isset($result['status']) && $result['status'] === 'success' && isset($result['batch_id'])) {
            // Redirect ke halaman review jika sukses
            $this->redirect('/admin/upload/review/' . $result['batch_id']);
        } else {
            // Jika gagal, pastikan ada pesan error yang jelas
            $errorMessage = $result['message'] ?? 'Terjadi kesalahan yang tidak diketahui saat memproses file.';
            Session::set('flash_message', ['type' => 'error', 'message' => $errorMessage]);
            $this->redirect('/admin/upload/questions');
        }
    }

    // Menampilkan halaman review
    public function showReviewPage(int $id)
    {
        $batch = $this->batchModel->findById($id);
        if (!$batch) {
            Session::set('flash_message', ['type' => 'error', 'message' => 'Batch dengan ID ' . $id . ' tidak ditemukan.']);
            $this->redirect('/admin/dashboard');
            return;
        }

        $stagedQuestions = $this->stagingModel->findByBatchId($id);
        $flashMessage = Session::get('flash_message');
        Session::set('flash_message', null);

        $this->render('admin/uploads/review', [
            'title' => 'Review Batch #' . $id,
            'batch' => $batch,
            'stagedQuestions' => $stagedQuestions,
            'flash_message' => $flashMessage
        ], 'layout/admin');
    }

    // Memproses upload ZIP
    public function handleZipUpload(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['zip_file']) || $_FILES['zip_file']['error'] !== UPLOAD_ERR_OK) {
            Session::set('flash_message', ['type' => 'error', 'message' => 'Upload ZIP gagal. Pastikan Anda telah memilih file yang valid.']);
        } else {
            $result = $this->batchService->processImageZipUpload($id, $_FILES['zip_file']);
            Session::set('flash_message', $result);
        }
        $this->redirect('/admin/upload/review/' . $id);
    }

    // Finalisasi Batch
    public function finalize(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             $this->redirect('/admin/upload/review/' . $id);
             return;
        }

        $success = $this->batchService->finalizeBatch($id);
        if ($success) {
            Session::set('flash_message', ['type' => 'success', 'message' => 'Batch #' . $id . ' berhasil difinalisasi dan semua data telah masuk ke sistem.']);
            $this->redirect('/admin/dashboard'); // Redirect ke dashboard setelah finalisasi sukses
        } else {
            Session::set('flash_message', ['type' => 'error', 'message' => 'Terjadi kesalahan saat finalisasi batch. Silakan cek log untuk detail.']);
            $this->redirect('/admin/upload/review/' . $id);
        }
    }
}
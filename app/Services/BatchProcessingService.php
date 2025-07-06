<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\QuestionUploadBatch;
use App\Models\StagingQuestion;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Answer;
use ZipArchive;

class BatchProcessingService
{
    private QuestionUploadBatch $batchModel;
    private StagingQuestion $stagingModel;
    private Subject $subjectModel;
    private Question $questionModel;
    private Answer $answerModel;

    public function __construct()
    {
        $this->batchModel = new QuestionUploadBatch();
        $this->stagingModel = new StagingQuestion();
        $this->subjectModel = new Subject();
        $this->questionModel = new Question();
        $this->answerModel = new Answer();
    }

    /**
     * TAHAP 1: Memproses upload file Excel dan memasukkannya ke tabel staging.
     * Mengembalikan ID batch untuk proses selanjutnya.
     */
    public function stageExcelUpload(array $file, int $uploaderId): array
    {
        $allowedMimes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        if (!in_array($file['type'], $allowedMimes)) {
            // PERUBAHAN: 'status' menjadi 'type'
            return ['type' => 'error', 'message' => 'Format file tidak valid. Harap upload file .xlsx atau .xls.'];
        }

        $batchId = $this->batchModel->create($uploaderId, $file['name']);
        if (!$batchId) {
            // PERUBAHAN: 'status' menjadi 'type'
            return ['type' => 'error', 'message' => 'Gagal membuat batch upload di database.'];
        }

        try {
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $stagingData = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                // --- Konversi Format Excel Lengkap ke JSON ---
                $answersArray = [];
                $answerTextCols = ['C', 'D', 'E', 'F', 'G'];
                $answerScoreCols = ['I', 'J', 'K', 'L', 'M']; // Kolom skor baru
                $correctAnswerColChar = strtoupper(trim($sheet->getCell('H' . $row)->getValue())); // Jawaban benar dari kolom H

                foreach ($answerTextCols as $idx => $colChar) {
                    $answerText = trim($sheet->getCell($colChar . $row)->getValue());
                    if (empty($answerText)) continue;

                    $scoreColChar = $answerScoreCols[$idx];
                    $answerScoreValue = trim($sheet->getCell($scoreColChar . $row)->getValue());

                    $answersArray[] = [
                        'text' => $answerText,
                        // Tentukan is_correct berdasarkan perbandingan dengan kolom H
                        'is_correct' => ($colChar === $correctAnswerColChar), 
                        'image' => null, // Placeholder untuk gambar
                        // Ambil skor dari kolom yang sesuai
                        'score' => is_numeric($answerScoreValue) ? (float)$answerScoreValue : null
                    ];
                }
                $answersJson = json_encode($answersArray);
                // --- Akhir Konversi ---

                $stagingData[] = [
                    'batch_id' => $batchId,
                    'row_number_in_excel' => $row,
                    'subject_name' => trim($sheet->getCell('A' . $row)->getValue()),
                    'question_text' => trim($sheet->getCell('B' . $row)->getValue()),
                    'answers_data' => $answersJson, // JSON sekarang berisi is_correct dan score
                    'explanation' => trim($sheet->getCell('N' . $row)->getValue()), // Penjelasan dari kolom N
                    'question_image_filename' => null,
                    'explanation_image_filename' => null,
                ];
            }
            
            $this->stagingModel->insertBatch($stagingData);
            $this->batchModel->updateStatus($batchId, 'pending_review');
            
            return ['status' => 'success', 'batch_id' => $batchId];

        } catch (\Exception $e) {
            // ... (error handling tetap sama)
            $this->batchModel->updateStatus($batchId, 'failed');
            error_log('Excel Staging Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage()];
        }
    }

    /**
     * TAHAP 2: Memproses upload file ZIP, mengekstrak, dan mencocokkan dengan data staging.
     */

    public function processImageZipUpload(int $batchId, array $zipFile): array
    {
        $allowedZipMimes = [
    'application/zip',
    'application/x-zip-compressed',
    'multipart/x-zip',
    'application/octet-stream' // Sebagai fallback untuk beberapa sistem
];

if (!in_array($zipFile['type'], $allowedZipMimes)) {
     // Berikan pesan error yang menyertakan tipe file yang terdeteksi untuk debugging
     return ['status' => 'error', 'message' => 'Format file tidak valid. Harap unggah file .zip. (Tipe terdeteksi: ' . htmlspecialchars($zipFile['type']) . ')'];
}

        $uploadDir = __DIR__ . '/../../storage/uploads/staging/' . $batchId . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $zip = new ZipArchive;
        if ($zip->open($zipFile['tmp_name']) !== TRUE) {
            // PERUBAHAN: 'status' menjadi 'type'
            return ['type' => 'error', 'message' => 'Gagal membuka file ZIP.'];
        }

        // Simpan daftar nama file yang diekstrak untuk dipindai
        $extractedFiles = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            // Hanya ekstrak file, bukan direktori, dan pastikan file gambar
            if (substr($filename, -1) !== '/' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $filename)) {
                $zip->extractTo($uploadDir, $filename);
                $extractedFiles[] = $filename;
            }
        }
        $zip->close();
        
        $stagedQuestions = $this->stagingModel->findByBatchId($batchId);
        
        foreach ($stagedQuestions as $staged) {
            $rowNum = $staged['row_number_in_excel'];
            $stagingQuestionId = $staged['staging_question_id'];
            
            // Cari file yang cocok dengan pola untuk baris ini
            foreach ($extractedFiles as $filename) {
                $fileBasename = pathinfo($filename, PATHINFO_FILENAME); // e.g., "2-q"

                if (preg_match('/^' . $rowNum . '-(q|e|a|b|c|d|e)$/i', $fileBasename, $matches)) {
                    $type = strtolower($matches[1]);

                    if ($type === 'q') {
                        $this->stagingModel->updateImageFilename($stagingQuestionId, 'question_image_filename', $filename);
                    } elseif ($type === 'e') {
                        $this->stagingModel->updateImageFilename($stagingQuestionId, 'explanation_image_filename', $filename);
                    } else { // Jawaban
                        $answers = json_decode($this->stagingModel->getAnswersData($stagingQuestionId), true); // Perlu method baru
                        $answerIndex = array_search($type, ['a', 'b', 'c', 'd', 'e']);
                        if (isset($answers[$answerIndex])) {
                            $answers[$answerIndex]['image'] = $filename;
                            $this->stagingModel->updateAnswersData($stagingQuestionId, json_encode($answers));
                        }
                    }
                }
            }
        }
        
        $this->batchModel->updateZipFilename($batchId, $zipFile['name']);
        // PERUBAHAN: 'status' menjadi 'type'
        return ['type' => 'success', 'message' => 'File ZIP berhasil diunggah dan gambar dicocokkan.'];
    }

    /**
     * TAHAP 3: Memindahkan data dari staging ke tabel produksi.
     */
    public function finalizeBatch(int $batchId): bool
    {
        $stagedQuestions = $this->stagingModel->findByBatchId($batchId);
        $stagingDir = __DIR__ . '/../../storage/uploads/staging/' . $batchId . '/';
        $productionDir = __DIR__ . '/../../storage/uploads/questions/';
        if (!is_dir($productionDir)) mkdir($productionDir, 0777, true);

        foreach ($stagedQuestions as $staged) {
            $subjectId = $this->subjectModel->findOrCreateByName($staged['subject_name']);
            
            $questionData = [
                'subject_id' => $subjectId,
                'question_text' => $staged['question_text'],
                'explanation' => $staged['explanation']
            ];

            // Pindahkan dan rename file gambar soal
            if ($staged['question_image_filename']) {
                $oldPath = $stagingDir . $staged['question_image_filename'];
                $newFilename = $batchId . '_q_' . $staged['row_number_in_excel'] . '.' . pathinfo($oldPath, PATHINFO_EXTENSION);
                if (file_exists($oldPath) && rename($oldPath, $productionDir . $newFilename)) {
                    $questionData['question_image_url'] = 'questions/' . $newFilename;
                }
            }

            // Pindahkan dan rename file gambar penjelasan
            if ($staged['explanation_image_filename']) {
                $oldPath = $stagingDir . $staged['explanation_image_filename'];
                $newFilename = $batchId . '_e_' . $staged['row_number_in_excel'] . '.' . pathinfo($oldPath, PATHINFO_EXTENSION);
                 if (file_exists($oldPath) && rename($oldPath, $productionDir . $newFilename)) {
                    $questionData['explanation_image_url'] = 'questions/' . $newFilename;
                }
            }

            $newQuestionId = $this->questionModel->create($questionData);
            if ($newQuestionId) {
                $answers = json_decode($staged['answers_data'], true); // JSON dari staging sudah berisi 'is_correct' dan 'score'
if (is_array($answers)) {
    foreach ($answers as $idx => $answerData) {
                        // Pindahkan gambar jawaban jika ada
                        if (!empty($answerData['image'])) {
                             $oldPath = $stagingDir . $answerData['image'];
                             $answerOptionChar = chr(65 + $idx); // A, B, C...
                             $newFilename = $batchId . '_ans_' . $staged['row_number_in_excel'] . '_' . $answerOptionChar . '.' . pathinfo($oldPath, PATHINFO_EXTENSION);
                             if (file_exists($oldPath) && rename($oldPath, $productionDir . $newFilename)) {
                                $answerData['answer_image_url'] = 'questions/' . $newFilename;
                            }
                        }
                        $this->answerModel->create($newQuestionId, $answerData); 
                    }
                }
            }
        }
        
        $this->batchModel->updateStatus($batchId, 'completed');
        return true;
    }
}
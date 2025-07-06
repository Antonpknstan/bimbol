<?php
namespace App\Models;

class QuestionUploadBatch extends BaseModel
{
    /**
     * Membuat entri baru untuk sebuah batch upload dan mengembalikan ID-nya.
     *
     * @param int $uploaderId ID pengguna yang mengunggah
     * @param string $originalFilename Nama file asli
     * @return int|false ID batch yang baru dibuat, atau false jika gagal.
     */
    public function create(int $uploaderId, string $originalFilename): int|false
    {
        $sql = "INSERT INTO question_upload_batches (uploader_user_id, original_filename_excel, status) 
                VALUES (:uploader_user_id, :original_filename, 'processing')";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'uploader_user_id' => $uploaderId,
            'original_filename' => $originalFilename
        ]);

        if ($success) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Memperbarui status dari sebuah batch upload.
     *
     * @param int $batchId ID batch yang akan diperbarui
     * @param string $status Status baru (e.g., 'completed', 'failed')
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateStatus(int $batchId, string $status): bool
    {
        $sql = "UPDATE question_upload_batches SET status = :status, processed_at = NOW() WHERE batch_id = :batch_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'batch_id' => $batchId
        ]);
    }

    // Tambahkan method ini
public function updateZipFilename(int $batchId, string $zipFilename): bool
{
    $sql = "UPDATE question_upload_batches SET original_filename_zip = :zip_filename WHERE batch_id = :batch_id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute(['zip_filename' => $zipFilename, 'batch_id' => $batchId]);
}

// Tambahkan juga method untuk mengambil detail batch
public function findById(int $batchId)
{
    $stmt = $this->db->prepare("SELECT * FROM question_upload_batches WHERE batch_id = :id");
    $stmt->execute(['id' => $batchId]);
    return $stmt->fetch();
}
}
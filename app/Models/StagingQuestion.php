<?php
namespace App\Models;

class StagingQuestion extends BaseModel
{
    /**
     * Memasukkan sekumpulan data pertanyaan dari Excel ke tabel staging.
     * Menggunakan transaksi untuk memastikan integritas data.
     *
     * @param array $stagingData Array asosiatif berisi data staging.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function insertBatch(array $stagingData): bool
    {
        if (empty($stagingData)) {
            return true; // Tidak ada yang perlu diinsert
        }

        $sql = "INSERT INTO staging_questions 
                (batch_id, row_number_in_excel, subject_name, question_text, answers_data, explanation) 
                VALUES (:batch_id, :row_number_in_excel, :subject_name, :question_text, :answers_data, :explanation)";
        
        $stmt = $this->db->prepare($sql);

        $this->db->beginTransaction();
        try {
            foreach ($stagingData as $data) {
                $stmt->execute([
                    ':batch_id' => $data['batch_id'],
                    ':row_number_in_excel' => $data['row_number_in_excel'],
                    ':subject_name' => $data['subject_name'],
                    ':question_text' => $data['question_text'],
                    ':answers_data' => $data['answers_data'],
                    ':explanation' => $data['explanation']
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Failed to insert staging data: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil semua data pertanyaan dari tabel staging berdasarkan ID batch.
     *
     * @param int $batchId
     * @return array
     */
    public function findByBatchId(int $batchId): array
    {
        $sql = "SELECT * FROM staging_questions WHERE batch_id = :batch_id ORDER BY row_number_in_excel ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['batch_id' => $batchId]);
        return $stmt->fetchAll();
    }

    // Tambahkan method ini
public function updateImageFilename(int $stagingQuestionId, string $column, string $filename): bool
{
    // Whitelist kolom untuk keamanan
    if (!in_array($column, ['question_image_filename', 'explanation_image_filename'])) {
        return false;
    }
    $sql = "UPDATE staging_questions SET `$column` = :filename WHERE staging_question_id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute(['filename' => $filename, 'id' => $stagingQuestionId]);
}

public function updateAnswersData(int $stagingQuestionId, string $jsonAnswers): bool
    {
        $sql = "UPDATE staging_questions SET answers_data = :answers_data WHERE staging_question_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['answers_data' => $jsonAnswers, 'id' => $stagingQuestionId]);
    }

    public function getAnswersData(int $stagingQuestionId): ?string
    {
        $stmt = $this->db->prepare("SELECT answers_data FROM staging_questions WHERE staging_question_id = :id");
        $stmt->execute(['id' => $stagingQuestionId]);
        $result = $stmt->fetchColumn();
        return $result ?: null;
    }
}
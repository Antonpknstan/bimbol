<?php
namespace App\Models;

class Question extends BaseModel
{
    public function findById(int $id) 
    {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE question_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Membuat entri pertanyaan baru dan mengembalikan ID-nya.
     *
     * @param array $data Data pertanyaan (e.g., ['subject_id' => ..., 'question_text' => ...])
     * @return int|false ID pertanyaan yang baru dibuat, atau false jika gagal.
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO questions (subject_id, question_text, question_image_url, explanation, explanation_image_url) 
                VALUES (:subject_id, :question_text, :question_image_url, :explanation, :explanation_image_url)";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'subject_id' => $data['subject_id'],
            'question_text' => $data['question_text'] ?? null,
            'question_image_url' => $data['question_image_url'] ?? null,
            'explanation' => $data['explanation'] ?? null,
            'explanation_image_url' => $data['explanation_image_url'] ?? null
        ]);

        if ($success) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }
}
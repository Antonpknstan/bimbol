<?php
namespace App\Models;

class Answer extends BaseModel
{
    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE answer_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByQuestionId(int $questionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE question_id = :question_id");
        $stmt->execute(['question_id' => $questionId]);
        return $stmt->fetchAll();
    }

    public function getAnswersForQuestions(array $questionIds): array
    {
        if (empty($questionIds)) {
            return [];
        }
        $inQuery = implode(',', array_fill(0, count($questionIds), '?'));
        $sql = "SELECT answer_id, question_id, answer_text, is_correct 
                FROM answers 
                WHERE question_id IN ($inQuery)";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($questionIds);
        // Menggunakan FETCH_GROUP untuk mengelompokkan hasil secara otomatis berdasarkan kolom pertama (question_id)
        return $stmt->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_ASSOC);
    }

    public function create(int $questionId, array $data): bool
    {
        $sql = "INSERT INTO answers (question_id, answer_text, answer_image_url, is_correct, score_value) 
                VALUES (:question_id, :answer_text, :answer_image_url, :is_correct, :score_value)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
        'question_id' => $questionId,
        'answer_text' => $data['text'] ?? null,
        'answer_image_url' => $data['answer_image_url'] ?? null,
        'is_correct' => (bool) ($data['is_correct'] ?? false), // Sudah menangani is_correct
        'score_value' => $data['score'] ?? null // Sudah menangani score
    ]);
    }
}
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
}
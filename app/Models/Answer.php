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
}
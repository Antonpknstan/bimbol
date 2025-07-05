<?php
namespace App\Models;

class Assessment extends BaseModel
{
    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM assessments WHERE assessment_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getQuestionsWithAnswers(int $assessmentId)
    {
        $sql = "SELECT 
                    q.question_id, q.question_text, q.question_image_url, 
                    a.answer_id, a.answer_text, a.answer_image_url, a.is_correct, a.score_value,
                    aq.order_index, aq.points_correct, aq.points_incorrect
                FROM assessment_questions aq
                JOIN questions q ON aq.question_id = q.question_id
                LEFT JOIN answers a ON q.question_id = a.question_id
                WHERE aq.assessment_id = :assessment_id
                ORDER BY aq.order_index ASC, a.answer_id ASC"; // Order answers by ID for consistency
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['assessment_id' => $assessmentId]);
        return $stmt->fetchAll();
    }
}
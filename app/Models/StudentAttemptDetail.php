<?php
namespace App\Models;

class StudentAttemptDetail extends BaseModel
{
    public function saveDetails(array $details)
    {
        if (empty($details)) {
            return false;
        }
        $sql = "INSERT INTO student_attempt_details 
                (attempt_id, question_id, chosen_answer_id, is_correct, points_earned) 
                VALUES (:attempt_id, :question_id, :chosen_answer_id, :is_correct, :points_earned)";
        $stmt = $this->db->prepare($sql);
        
        $this->db->beginTransaction();
        try {
            foreach ($details as $detail) {
                $stmt->execute([
                    'attempt_id' => $detail['attempt_id'],
                    'question_id' => $detail['question_id'],
                    'chosen_answer_id' => $detail['chosen_answer_id'],
                    'is_correct' => $detail['is_correct'],
                    'points_earned' => $detail['points_earned']
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            // Log the error
            error_log("Failed to save attempt details: " . $e->getMessage());
            return false;
        }
    }

    public function getDetailsByAttemptId(int $attemptId)
    {
        $sql = "SELECT 
                    sad.*, 
                    q.question_text, q.explanation, q.explanation_image_url,
                    a_chosen.answer_text as chosen_answer_text, a_chosen.answer_image_url as chosen_answer_image_url
                FROM student_attempt_details sad
                JOIN questions q ON sad.question_id = q.question_id
                LEFT JOIN answers a_chosen ON sad.chosen_answer_id = a_chosen.answer_id
                WHERE sad.attempt_id = :attempt_id
                ORDER BY q.question_id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['attempt_id' => $attemptId]);
        return $stmt->fetchAll();
    }
}
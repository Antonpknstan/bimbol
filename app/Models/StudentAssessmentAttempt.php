<?php
namespace App\Models;

class StudentAssessmentAttempt extends BaseModel
{
    public function create(int $assessmentId, int $userId): ?int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO student_assessment_attempts (assessment_id, user_id, start_time, status) VALUES (:assessment_id, :user_id, NOW(), 'in_progress')"
        );
        $stmt->execute([
            'assessment_id' => $assessmentId,
            'user_id' => $userId
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $attemptId)
    {
        $sql = "SELECT saa.*, a.title as assessment_title, a.type as assessment_type, a.time_limit_minutes
                FROM student_assessment_attempts saa
                JOIN assessments a ON saa.assessment_id = a.assessment_id
                WHERE saa.attempt_id = :attempt_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['attempt_id' => $attemptId]);
        return $stmt->fetch();
    }

    public function updateAttemptResult(int $attemptId, float $score, int $totalCorrect, int $totalIncorrect, int $totalUnanswered)
    {
        $stmt = $this->db->prepare(
            "UPDATE student_assessment_attempts SET 
             end_time = NOW(), status = 'completed', score = :score, 
             total_correct = :total_correct, total_incorrect = :total_incorrect, 
             total_unanswered = :total_unanswered 
             WHERE attempt_id = :attempt_id"
        );
        return $stmt->execute([
            'score' => $score,
            'total_correct' => $totalCorrect,
            'total_incorrect' => $totalIncorrect,
            'total_unanswered' => $totalUnanswered,
            'attempt_id' => $attemptId
        ]);
    }
}
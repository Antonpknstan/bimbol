<?php
namespace App\Models;

class UserLessonProgress extends BaseModel
{
    public function getProgress(int $userId, int $lessonId)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_lesson_progress WHERE user_id = :user_id AND lesson_id = :lesson_id");
        $stmt->execute(['user_id' => $userId, 'lesson_id' => $lessonId]);
        return $stmt->fetch();
    }

    public function markAsCompleted(int $userId, int $lessonId): bool
    {
        $existingProgress = $this->getProgress($userId, $lessonId);
        if ($existingProgress) {
            if ($existingProgress['is_completed']) {
                return true; // Already completed
            }
            $sql = "UPDATE user_lesson_progress SET is_completed = TRUE, completed_at = NOW() WHERE user_id = :user_id AND lesson_id = :lesson_id";
        } else {
            $sql = "INSERT INTO user_lesson_progress (user_id, lesson_id, is_completed, completed_at) VALUES (:user_id, :lesson_id, TRUE, NOW())";
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId, 'lesson_id' => $lessonId]);
    }
    
    public function getProgressByModuleIds(int $userId, array $moduleIds) {
        if (empty($moduleIds)) {
            return [];
        }
        $inQuery = implode(',', array_fill(0, count($moduleIds), '?'));
        $sql = "SELECT ulp.lesson_id, ulp.is_completed, ulp.completed_at, l.module_id
                FROM user_lesson_progress ulp
                JOIN lessons l ON ulp.lesson_id = l.lesson_id
                WHERE ulp.user_id = :user_id AND l.module_id IN ($inQuery)";
        $stmt = $this->db->prepare($sql);
        $params = array_merge(['user_id' => $userId], $moduleIds);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
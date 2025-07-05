<?php
namespace App\Models;

class SkillMastery extends BaseModel
{
    public function getMastery(int $userId, int $subjectId)
    {
        $stmt = $this->db->prepare("SELECT * FROM skill_mastery WHERE user_id = :user_id AND subject_id = :subject_id");
        $stmt->execute(['user_id' => $userId, 'subject_id' => $subjectId]);
        return $stmt->fetch();
    }

    public function updateMastery(int $userId, int $subjectId, float $masteryLevel): bool
    {
        $existingMastery = $this->getMastery($userId, $subjectId);
        if ($existingMastery) {
            $sql = "UPDATE skill_mastery SET mastery_level = :mastery_level, last_updated = NOW() WHERE user_id = :user_id AND subject_id = :subject_id";
        } else {
            $sql = "INSERT INTO skill_mastery (user_id, subject_id, mastery_level, last_updated) VALUES (:user_id, :subject_id, :mastery_level, NOW())";
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'subject_id' => $subjectId,
            'mastery_level' => $masteryLevel
        ]);
    }
}
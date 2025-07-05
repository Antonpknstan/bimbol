<?php
namespace App\Models;

class Module extends BaseModel
{
    public function findByCourseId(int $courseId)
    {
        $stmt = $this->db->prepare("SELECT * FROM modules WHERE course_id = :course_id ORDER BY order_index ASC");
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll();
    }
}
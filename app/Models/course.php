<?php
namespace App\Models;

class Course extends BaseModel
{
    public function findAllWithSubject()
    {
        $sql = "SELECT c.*, s.name as subject_name 
                FROM courses c 
                LEFT JOIN subjects s ON c.subject_id = s.subject_id
                ORDER BY c.title ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE course_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
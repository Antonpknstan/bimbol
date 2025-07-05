<?php
namespace App\Models;

class Lesson extends BaseModel
{
    public function findByModuleIds(array $moduleIds)
    {
        if (empty($moduleIds)) {
            return [];
        }
        $inQuery = implode(',', array_fill(0, count($moduleIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE module_id IN ($inQuery) ORDER BY order_index ASC");
        $stmt->execute($moduleIds);
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE lesson_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
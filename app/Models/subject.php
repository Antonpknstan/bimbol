<?php
namespace App\Models;

class Subject extends BaseModel
{
    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM subjects ORDER BY name ASC");
        return $stmt->fetchAll();
    }
}
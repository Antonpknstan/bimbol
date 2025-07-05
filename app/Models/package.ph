<?php
namespace App\Models;

class Package extends BaseModel
{
    public function findAllActive()
    {
        $stmt = $this->db->query("SELECT * FROM packages WHERE status = 'active' ORDER BY price ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM packages WHERE package_id = :id AND status = 'active'");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
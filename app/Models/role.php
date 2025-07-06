<?php
namespace App\Models;

/**
 * Class Role
 * Handles data operations for the `roles` table.
 */
class Role extends BaseModel
{
    /**
     * Mengambil semua peran yang ada di sistem.
     * Secara opsional, bisa mengecualikan peran tertentu seperti 'Student'.
     *
     * @param array $excludeNames Array berisi nama peran yang ingin dikecualikan.
     * @return array
     */
    public function findAll(array $excludeNames = ['Student']): array
    {
        $sql = "SELECT * FROM roles";

        if (!empty($excludeNames)) {
            // Membuat placeholder (e.g., ?, ?, ?) untuk klausa NOT IN
            $placeholders = implode(',', array_fill(0, count($excludeNames), '?'));
            $sql .= " WHERE name NOT IN ($placeholders)";
        }

        $sql .= " ORDER BY name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($excludeNames);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Menemukan sebuah peran berdasarkan ID-nya.
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE role_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
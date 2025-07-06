<?php
namespace App\Models;

class Report extends BaseModel
{
    /**
     * Membuat laporan masalah baru dari pengguna.
     */
    public function create(int $userId, string $reportType, int $itemId, string $description): bool
    {
        $sql = "INSERT INTO reports (user_id, report_type, item_id_ref, problem_description, status) 
                VALUES (:user_id, :report_type, :item_id_ref, :problem_description, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'report_type' => $reportType,
            'item_id_ref' => $itemId,
            'problem_description' => $description
        ]);
    }

    /**
     * Mengambil semua laporan, diurutkan dari yang terbaru, dengan detail user.
     */
    public function findAllWithDetails(): array
    {
        $sql = "SELECT r.*, u.username as reporter_username 
                FROM reports r
                JOIN users u ON r.user_id = u.user_id
                ORDER BY r.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Memperbarui status sebuah laporan.
     */
    public function updateStatus(int $reportId, string $status, int $resolverId): bool
    {
        $sql = "UPDATE reports 
                SET status = :status, resolved_by_user_id = :resolver_id, resolved_at = NOW() 
                WHERE report_id = :report_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'resolver_id' => $resolverId,
            'report_id' => $reportId
        ]);
    }
}
<?php
namespace App\Models;

class Notification extends BaseModel
{
    /**
     * Membuat notifikasi baru untuk seorang pengguna.
     */
    public function create(int $userId, string $title, string $message, ?string $itemType = null, ?int $itemId = null): bool
    {
        $sql = "INSERT INTO notifications (user_id, title, message, related_item_type, related_item_id) 
                VALUES (:user_id, :title, :message, :item_type, :item_id)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'item_type' => $itemType,
            'item_id' => $itemId
        ]);
    }

    /**
     * Mengambil notifikasi untuk seorang pengguna.
     */
    public function findByUser(int $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY sent_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menghitung notifikasi yang belum dibaca.
     */
    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = false");
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Menandai semua notifikasi pengguna sebagai telah dibaca.
     */
    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = true WHERE user_id = :user_id AND is_read = false");
        return $stmt->execute(['user_id' => $userId]);
    }
}
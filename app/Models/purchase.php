<?php
namespace App\Models;

class Purchase extends BaseModel
{
     /**
     * Membuat entri pembelian baru.
     * @param array $data Data pembelian
     * @return int|false ID pembelian yang baru dibuat, atau false jika gagal.
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO purchases (transaction_id, user_id, package_id, price_at_purchase, status) 
                VALUES (:transaction_id, :user_id, :package_id, :price_at_purchase, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'transaction_id' => $data['transaction_id'],
            'user_id' => $data['user_id'],
            'package_id' => $data['package_id'],
            'price_at_purchase' => $data['price_at_purchase'],
        ]);

        if ($success) {
            return (int) $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function findByTransactionId(string $transactionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM purchases WHERE transaction_id = :id");
        $stmt->execute(['id' => $transactionId]);
        return $stmt->fetch();
    }

    public function updateStatusToSuccess(string $transactionId, int $durationDays)
    {
        // Set tanggal kedaluwarsa berdasarkan durasi paket
        $expiryDate = (new \DateTime())->add(new \DateInterval("P{$durationDays}D"))->format('Y-m-d H:i:s');
        
        $sql = "UPDATE purchases SET status = 'success', expiry_date = :expiry_date WHERE transaction_id = :transaction_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['transaction_id' => $transactionId, 'expiry_date' => $expiryDate]);
    }

    public function findByUser(int $userId)
    {
        $sql = "SELECT p.*, pkg.name as package_name 
                FROM purchases p
                JOIN packages pkg ON p.package_id = pkg.package_id
                WHERE p.user_id = :user_id 
                ORDER BY p.purchase_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function countByStatus(string $status): int
{
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM purchases WHERE status = :status");
    $stmt->execute(['status' => $status]);
    return (int) $stmt->fetchColumn();
}
}
<?php
namespace App\Models;

// Pastikan BaseModel juga sudah benar
class User extends BaseModel
{
    public function findByUsernameOrEmail(string $identifier)
{
    // GANTI QUERY: Gunakan dua nama placeholder yang berbeda
    $sql = "SELECT * FROM users WHERE username = :username OR email = :email";

    try {
        $stmt = $this->db->prepare($sql);

        // GANTI EXECUTE: Berikan nilai untuk setiap placeholder
        $stmt->execute([
            ':username' => $identifier,
            ':email'     => $identifier
        ]);

        return $stmt->fetch();

    } catch (\PDOException $e) {
        // ... (kode penanganan error bisa tetap ada untuk jaga-jaga) ...
        die("Database Error di Method findByUsernameOrEmail(): " . $e->getMessage());
    }
}

    /**
     * Membuat user baru di database.
     * @param array $data Data dari form POST
     * @return bool True jika berhasil, false jika gagal.
     */
    public function create(array $data): bool
{
    // === PERBARUI QUERY INSERT ===
    $stmt = $this->db->prepare(
        "INSERT INTO users (full_name, username, email, phone_number, password_hash) VALUES (:full_name, :username, :email, :phone_number, :password_hash)"
    );

    // === PERBARUI ARRAY EXECUTE ===
    return $stmt->execute([
        'full_name' => $data['full_name'],
        'username' => $data['username'],
        'email' => $data['email'],
        // Gunakan null coalescing operator untuk menangani jika field kosong
        'phone_number' => $data['phone_number'] ?: null, 
        'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
    ]);
}

public function hasPermission(int $userId, string $permissionName): bool
{
    $sql = "SELECT COUNT(*)
            FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.permission_id
            WHERE ur.user_id = :user_id AND p.name = :permission_name";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'permission_name' => $permissionName
    ]);

    return $stmt->fetchColumn() > 0;
}

public function findById(int $id)
{
    $stmt = $this->db->prepare("SELECT user_id, full_name, username, email FROM users WHERE user_id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

public function findAllWithRoles()
{
    $sql = "SELECT u.user_id, u.full_name, u.username, u.email, GROUP_CONCAT(r.name SEPARATOR ', ') as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.user_id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.role_id
            GROUP BY u.user_id
            ORDER BY u.user_id ASC";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
}

public function getRoleIds(int $userId): array
{
    $stmt = $this->db->prepare("SELECT role_id FROM user_roles WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    // fetchAll dengan PDO::FETCH_COLUMN akan mengembalikan array 1 dimensi berisi role_id
    return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
}

public function syncRoles(int $userId, array $roleIds): bool
{
    $this->db->beginTransaction();
    try {
        // 1. Hapus semua peran lama pengguna ini
        $stmt_delete = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
        $stmt_delete->execute(['user_id' => $userId]);

        // 2. Insert peran baru jika ada yang dipilih
        if (!empty($roleIds)) {
            $stmt_insert = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
            foreach ($roleIds as $roleId) {
                $stmt_insert->execute([
                    'user_id' => $userId,
                    'role_id' => (int)$roleId // Pastikan integer
                ]);
            }
        }
        
        $this->db->commit();
        return true;
    } catch (\PDOException $e) {
        $this->db->rollBack();
        error_log("Failed to sync roles: " . $e->getMessage());
        return false;
    }
}

public function countAll(): int
{
    return (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
}

public function findRecent(int $limit = 5): array
{
    $stmt = $this->db->query("SELECT user_id, full_name, email, created_at FROM users ORDER BY created_at DESC LIMIT $limit");
    return $stmt->fetchAll();
}
}
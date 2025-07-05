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
}
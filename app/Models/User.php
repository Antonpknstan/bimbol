<?php
namespace App\Models;

class User extends BaseModel
{
    public function findByUsernameOrEmail(string $identifier)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :identifier OR email = :identifier");
        $stmt->execute(['identifier' => $identifier]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (full_name, username, email, password_hash) VALUES (:full_name, :username, :email, :password_hash)"
        );
        return $stmt->execute([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }
}
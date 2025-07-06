<?php
namespace App\Models;

class Subject extends BaseModel
{
    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM subjects ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Menemukan subjek berdasarkan nama. Jika tidak ditemukan, buat subjek baru.
     * Mengembalikan ID dari subjek yang ditemukan atau yang baru dibuat.
     *
     * @param string $name Nama subjek
     * @return int|null ID subjek, atau null jika gagal.
     */
    public function findOrCreateByName(string $name): ?int
    {
        // Trim spasi dan pastikan tidak kosong
        $name = trim($name);
        if (empty($name)) {
            return null;
        }

        // 1. Coba cari subjek yang sudah ada
        $stmt_find = $this->db->prepare("SELECT subject_id FROM subjects WHERE name = :name");
        $stmt_find->execute(['name' => $name]);
        $subject = $stmt_find->fetch();

        if ($subject) {
            // Jika ditemukan, kembalikan ID-nya
            return (int) $subject['subject_id'];
        } else {
            // 2. Jika tidak ditemukan, buat subjek baru
            $stmt_create = $this->db->prepare("INSERT INTO subjects (name) VALUES (:name)");
            $success = $stmt_create->execute(['name' => $name]);
            if ($success) {
                // Kembalikan ID dari subjek yang baru dibuat
                return (int) $this->db->lastInsertId();
            }
        }
        return null; // Gagal menemukan atau membuat
    }
}
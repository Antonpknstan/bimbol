<?php
namespace App\Models;

class Question extends BaseModel
{
    // Model ini mungkin tidak banyak punya method CRUD langsung
    // karena pertanyaan sering diakses via Assessment atau batch import.
    // Tapi, bisa ada method untuk mengambil detail pertanyaan tunggal jika diperlukan.
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE question_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
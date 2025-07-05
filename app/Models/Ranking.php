<?php
namespace App\Models;

class Ranking extends BaseModel
{
    /**
     * Menyimpan atau memperbarui data peringkat untuk seorang pengguna.
     */
    public function saveOrUpdateRanking(int $userId, int $attemptId, int $assessmentId, int $tryoutPeriodId, float $score): bool
    {
        // Cek apakah sudah ada ranking untuk user ini di tryout period ini
        $stmt_check = $this->db->prepare("SELECT ranking_id FROM rankings WHERE user_id = :user_id AND tryout_period_id = :tryout_period_id");
        $stmt_check->execute(['user_id' => $userId, 'tryout_period_id' => $tryoutPeriodId]);
        $existing = $stmt_check->fetch();

        if ($existing) {
            // Update jika sudah ada (misal: jika ada sistem re-take)
            $sql = "UPDATE rankings SET attempt_id = :attempt_id, score = :score WHERE ranking_id = :ranking_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'attempt_id' => $attemptId,
                'score' => $score,
                'ranking_id' => $existing['ranking_id']
            ]);
        } else {
            // Insert jika belum ada
            $sql = "INSERT INTO rankings (user_id, attempt_id, score, rank, assessment_id, tryout_period_id) 
                    VALUES (:user_id, :attempt_id, :score, 0, :assessment_id, :tryout_period_id)"; // Rank di-set 0 dulu
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'attempt_id' => $attemptId,
                'score' => $score,
                'assessment_id' => $assessmentId,
                'tryout_period_id' => $tryoutPeriodId,
            ]);
        }
    }

    /**
     * Menghitung ulang dan memperbarui kolom 'rank' untuk sebuah tryout period.
     * Ini adalah operasi yang intensif dan sebaiknya dijalankan secara terjadwal.
     */
    public function recalculateRanksForPeriod(int $tryoutPeriodId)
    {
        // Ambil semua entri peringkat, diurutkan berdasarkan skor tertinggi
        $stmt = $this->db->prepare("SELECT ranking_id FROM rankings WHERE tryout_period_id = :id ORDER BY score DESC");
        $stmt->execute(['id' => $tryoutPeriodId]);
        $rankings = $stmt->fetchAll();
        
        $this->db->beginTransaction();
        try {
            $rank = 1;
            $stmt_update = $this->db->prepare("UPDATE rankings SET rank = :rank WHERE ranking_id = :id");
            foreach ($rankings as $ranking) {
                $stmt_update->execute(['rank' => $rank, 'id' => $ranking['ranking_id']]);
                $rank++;
            }
            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Failed to recalculate ranks: " . $e->getMessage());
        }
    }

    /**
     * Mengambil data leaderboard untuk sebuah tryout period.
     */
    public function getLeaderboard(int $tryoutPeriodId, int $limit = 100)
    {
        $sql = "SELECT r.rank, r.score, u.username, u.full_name
                FROM rankings r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.tryout_period_id = :tryout_period_id AND r.rank > 0
                ORDER BY r.rank ASC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tryout_period_id', $tryoutPeriodId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
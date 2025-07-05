<?php
namespace App\Models;

class TryoutPeriod extends BaseModel
{
    public function findAllActive()
    {
        $now = date('Y-m-d H:i:s');
        // Menampilkan tryout yang sedang berlangsung atau yang akan datang
        $stmt = $this->db->prepare("SELECT tp.*, a.title as assessment_title 
                                    FROM tryout_periods tp
                                    JOIN assessments a ON tp.assessment_id = a.assessment_id
                                    WHERE tp.end_time > :now 
                                    ORDER BY tp.start_time ASC");
        $stmt->execute(['now' => $now]);
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tryout_periods WHERE tryout_period_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findActiveByAssessmentId(int $assessmentId)
    {
        $sql = "SELECT tryout_period_id FROM tryout_periods 
                WHERE assessment_id = :assessment_id 
                AND NOW() BETWEEN start_time AND end_time 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['assessment_id' => $assessmentId]);
        return $stmt->fetch();
    }
}
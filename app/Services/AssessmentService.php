<?php
namespace App\Services;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\Answer;
use App\Models\StudentAssessmentAttempt;
use App\Models\StudentAttemptDetail;
use App\Models\SkillMastery;
use App\Models\Subject;
use App\Models\TryoutPeriod;
use App\Models\Ranking;

class AssessmentService
{
    private Assessment $assessmentModel;
    private Question $questionModel;
    private Answer $answerModel;
    private StudentAssessmentAttempt $attemptModel;
    private StudentAttemptDetail $attemptDetailModel;
    private SkillMastery $skillMasteryModel;
    private Subject $subjectModel;
    private TryoutPeriod $tryoutPeriodModel; 
    private Ranking $rankingModel;

    public function __construct()
    {
        $this->assessmentModel = new Assessment();
        $this->questionModel = new Question();
        $this->answerModel = new Answer();
        $this->attemptModel = new StudentAssessmentAttempt();
        $this->attemptDetailModel = new StudentAttemptDetail();
        $this->skillMasteryModel = new SkillMastery();
        $this->subjectModel = new Subject();
        $this->tryoutPeriodModel = new TryoutPeriod();
        $this->rankingModel = new Ranking();
    }

    /**
     * Memulai upaya asesmen baru.
     * @return int|null ID upaya yang baru dibuat, atau null jika gagal.
     */
    public function startNewAttempt(int $assessmentId, int $userId): ?int
    {
        // Bisa tambahkan logika: cek apakah user punya akses ke asesmen ini
        // cek apakah sudah ada attempt aktif, dll.
        return $this->attemptModel->create($assessmentId, $userId);
    }

    /**
     * Mengambil detail asesmen dan pertanyaan untuk upaya tertentu.
     * Mengelompokkan jawaban ke pertanyaan.
     */
    public function getAssessmentForAttempt(int $attemptId)
    {
        $attempt = $this->attemptModel->findById($attemptId);
        if (!$attempt) {
            return null;
        }

        $rawQuestionsData = $this->assessmentModel->getQuestionsWithAnswers($attempt['assessment_id']);
        
        $questions = [];
        foreach ($rawQuestionsData as $row) {
            $questionId = $row['question_id'];
            if (!isset($questions[$questionId])) {
                $questions[$questionId] = [
                    'question_id' => $row['question_id'],
                    'question_text' => $row['question_text'],
                    'question_image_url' => $row['question_image_url'],
                    'order_index' => $row['order_index'],
                    'answers' => [],
                    'points_correct' => $row['points_correct'],
                    'points_incorrect' => $row['points_incorrect']
                ];
            }
            if ($row['answer_id']) { // Pastikan ada jawaban
                $questions[$questionId]['answers'][] = [
                    'answer_id' => $row['answer_id'],
                    'answer_text' => $row['answer_text'],
                    'answer_image_url' => $row['answer_image_url']
                ];
            }
        }

        $attempt['questions'] = array_values($questions); // Convert associative array to indexed array
        return $attempt;
    }

    /**
     * Memproses jawaban yang diserahkan dan menghitung skor.
     * @param int $attemptId ID upaya asesmen
     * @param array $submittedAnswers Array asosiatif: [question_id => chosen_answer_id]
     * @return array Hasil grading (score, correct_count, etc.)
     */
    public function gradeAttempt(int $attemptId, array $submittedAnswers): array
    {
        $attempt = $this->attemptModel->findById($attemptId);
        if (!$attempt) {
            return ['error' => 'Attempt not found.'];
        }

        $assessmentId = $attempt['assessment_id'];
        $rawQuestionsData = $this->assessmentModel->getQuestionsWithAnswers($assessmentId);
        
        $correctAnswers = [];
        $questionPoints = [];
        $questionSubjectIds = []; // Untuk skill mastery
        foreach ($rawQuestionsData as $row) {
            if (!isset($correctAnswers[$row['question_id']])) {
                $correctAnswers[$row['question_id']] = [];
                $questionPoints[$row['question_id']] = [
                    'correct' => $row['points_correct'],
                    'incorrect' => $row['points_incorrect']
                ];
                // Dapatkan subject_id dari pertanyaan
                $q = $this->questionModel->findById($row['question_id']);
                $questionSubjectIds[$row['question_id']] = $q['subject_id'];
            }
            if ($row['is_correct']) {
                $correctAnswers[$row['question_id']][] = $row['answer_id'];
            }
        }

        $totalCorrect = 0;
        $totalIncorrect = 0;
        $totalUnanswered = 0;
        $totalScore = 0.0;
        $attemptDetails = [];
        $subjectScores = []; // Untuk skill mastery
        $subjectTotalQuestions = [];

        foreach ($questionPoints as $qId => $points) {
            $chosenAnswerId = $submittedAnswers[$qId] ?? null;
            $isCorrect = false;
            $pointsEarned = 0.0;

            if ($chosenAnswerId === null) {
                $totalUnanswered++;
            } elseif (in_array($chosenAnswerId, $correctAnswers[$qId])) {
                $isCorrect = true;
                $totalCorrect++;
                $pointsEarned = $points['correct'];
            } else {
                $isCorrect = false;
                $totalIncorrect++;
                $pointsEarned = -$points['incorrect']; // Deduct points for incorrect answers
            }
            
            $totalScore += $pointsEarned;

            $attemptDetails[] = [
                'attempt_id' => $attemptId,
                'question_id' => $qId,
                'chosen_answer_id' => $chosenAnswerId,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned
            ];

            // Hitung skor per subjek untuk skill mastery
            $subjectId = $questionSubjectIds[$qId];
            if (!isset($subjectScores[$subjectId])) {
                $subjectScores[$subjectId] = 0;
                $subjectTotalQuestions[$subjectId] = 0;
            }
            if ($isCorrect) {
                $subjectScores[$subjectId]++;
            }
            $subjectTotalQuestions[$subjectId]++;
        }

        // Simpan detail upaya
        $this->attemptDetailModel->saveDetails($attemptDetails);

        // Update status upaya
        $this->attemptModel->updateAttemptResult($attemptId, $totalScore, $totalCorrect, $totalIncorrect, $totalUnanswered);

        // Update Skill Mastery (contoh sederhana: persentase benar per subjek)
        foreach ($subjectScores as $sId => $correctCount) {
            $totalQuestions = $subjectTotalQuestions[$sId];
            $masteryLevel = ($totalQuestions > 0) ? ($correctCount / $totalQuestions) * 100 : 0;
            $this->skillMasteryModel->updateMastery($attempt['user_id'], $sId, $masteryLevel);
        }

        if ($attempt['assessment_type'] === 'try_out') {
    // Memanggil method baru dari model, bukan mengakses DB secara langsung
    $tryoutPeriod = $this->tryoutPeriodModel->findActiveByAssessmentId($assessmentId);

    if ($tryoutPeriod) {
        // Simpan hasil ke tabel ranking
        $this->rankingModel->saveOrUpdateRanking(
            $attempt['user_id'],
            $attemptId,
            $assessmentId,
            $tryoutPeriod['tryout_period_id'],
            $totalScore
        );
        
        // Kalkulasi ulang peringkat.
        // NOTE: Di aplikasi produksi, ini sebaiknya dijalankan sebagai background job (cron job)
        // agar tidak memperlambat response ke user. Untuk proyek ini, kita jalankan langsung.
        $this->rankingModel->recalculateRanksForPeriod($tryoutPeriod['tryout_period_id']);
    }
}

        return [
            'score' => $totalScore,
            'total_correct' => $totalCorrect,
            'total_incorrect' => $totalIncorrect,
            'total_unanswered' => $totalUnanswered
        ];
    }

    /**
     * Mendapatkan hasil upaya asesmen dengan detail pertanyaan dan jawaban.
     */
    public function getAttemptResultDetails(int $attemptId)
    {
        $attempt = $this->attemptModel->findById($attemptId);
        if (!$attempt || $attempt['status'] !== 'completed') {
            return null; // Atau throw exception
        }

        $details = $this->attemptDetailModel->getDetailsByAttemptId($attemptId);
        $correctAnswers = []; // Simpan jawaban benar untuk setiap pertanyaan
        
        // Ambil semua jawaban untuk pertanyaan-pertanyaan ini
$questionIds = array_column($details, 'question_id');
if (!empty($questionIds)) {
    // Memanggil method baru dari model, bukan mengakses DB secara langsung
    $allAnswersGrouped = $this->answerModel->getAnswersForQuestions($questionIds);

    foreach ($allAnswersGrouped as $qId => $answers) {
        foreach ($answers as $ans) {
            if ($ans['is_correct']) {
                if (!isset($correctAnswers[$qId])) {
                    $correctAnswers[$qId] = [];
                }
                $correctAnswers[$qId][] = $ans['answer_text'];
            }
        }
    }
}


        foreach ($details as &$detail) {
            $detail['correct_answers'] = $correctAnswers[$detail['question_id']] ?? ['Tidak ada jawaban benar yang terdaftar.'];
        }

        $attempt['details'] = $details;
        return $attempt;
    }
}
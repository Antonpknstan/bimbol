<?php
namespace App\Controllers;

use App\Services\AssessmentService;
use App\Models\Assessment;
use App\Utils\Session;

class AssessmentController extends BaseController
{
    private AssessmentService $assessmentService;
    private Assessment $assessmentModel;

    public function __construct()
    {
        $this->assessmentService = new AssessmentService();
        $this->assessmentModel = new Assessment();
    }

    /**
     * Memulai asesmen baru.
     * @param int $assessmentId ID asesmen yang akan dimulai
     */
    public function start(int $assessmentId)
    {
        if (!Session::has('user')) {
            $this->redirect('/login');
            return;
        }

        $userId = Session::get('user')['id'];
        $attemptId = $this->assessmentService->startNewAttempt($assessmentId, $userId);

        if ($attemptId) {
            $this->redirect('/assessment/attempt/' . $attemptId);
        } else {
            // Error handling, misal redirect ke halaman error atau daftar asesmen
            $this->redirect('/dashboard'); // Atau halaman daftar asesmen
        }
    }

    /**
     * Menampilkan halaman pengerjaan asesmen.
     * @param int $attemptId ID upaya asesmen yang sedang berlangsung
     */
    public function showAttempt(int $attemptId)
    {
        if (!Session::has('user')) {
            $this->redirect('/login');
            return;
        }

        $attempt = $this->assessmentService->getAssessmentForAttempt($attemptId);
        
        if (!$attempt || $attempt['user_id'] !== Session::get('user')['id'] || $attempt['status'] !== 'in_progress') {
            $this->redirect('/dashboard'); // Atau halaman error/not allowed
            return;
        }
        
        $this->render('assessments/attempt', [
            'title' => $attempt['assessment_title'],
            'attempt' => $attempt
        ]);
    }

    /**
     * Memproses submit jawaban asesmen.
     * @param int $attemptId ID upaya asesmen yang disubmit
     */
    public function submit(int $attemptId)
    {
        if (!Session::has('user') || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        // Ambil jawaban dari POST request. Format: ['question_id' => 'chosen_answer_id']
        $submittedAnswers = $_POST['answers'] ?? []; 
        
        $result = $this->assessmentService->gradeAttempt($attemptId, $submittedAnswers);

        if (isset($result['error'])) {
            // Handle error grading
            $this->redirect('/dashboard'); // Atau tampilkan pesan error
            return;
        }

        $this->redirect('/assessment/result/' . $attemptId);
    }

    /**
     * Menampilkan hasil asesmen yang sudah selesai.
     * @param int $attemptId ID upaya asesmen yang telah selesai
     */
    public function showResult(int $attemptId)
    {
        if (!Session::has('user')) {
            $this->redirect('/login');
            return;
        }

        $attemptResult = $this->assessmentService->getAttemptResultDetails($attemptId);
        
        if (!$attemptResult || $attemptResult['user_id'] !== Session::get('user')['id']) {
            $this->redirect('/dashboard'); // Atau halaman error/not allowed
            return;
        }

        $this->render('assessments/result', [
            'title' => 'Hasil ' . $attemptResult['assessment_title'],
            'attempt' => $attemptResult
        ]);
    }
}
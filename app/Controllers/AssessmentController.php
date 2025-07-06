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
     * @param int $id ID asesmen yang akan dimulai
     */
    public function start(int $id)
    {
        if (!Session::has('user')) {
            $this->redirect('/login');
            return;
        }

        $userId = Session::get('user')['id'];
        $attemptId = $this->assessmentService->startNewAttempt($id, $userId);

        if ($attemptId) {
            $this->redirect('/assessment/attempt/' . $attemptId);
        } else {
            $this->redirect('/dashboard');
        }
    }

    /**
     * Menampilkan halaman pengerjaan asesmen.
     * @param int $id ID upaya asesmen yang sedang berlangsung
     */
    public function showAttempt(int $id) // PERUBAHAN
    {
        if (!Session::has('user')) {
            $this->redirect('/login');
            return;
        }

        $attempt = $this->assessmentService->getAssessmentForAttempt($id); // PERUBAHAN
        
        if (!$attempt || $attempt['user_id'] !== Session::get('user')['id'] || $attempt['status'] !== 'in_progress') {
            $this->redirect('/dashboard');
            return;
        }
        
        $this->render('assessments/attempt', [
            'title' => $attempt['assessment_title'],
            'attempt' => $attempt
        ]);
    }

    /**
     * Memproses submit jawaban asesmen.
     * @param int $id ID upaya asesmen yang disubmit
     */
    public function submit(int $id) // PERUBAHAN
    {
        if (!Session::has('user') || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $submittedAnswers = $_POST['answers'] ?? []; 
        
        $result = $this->assessmentService->gradeAttempt($id, $submittedAnswers); // PERUBAHAN

        if (isset($result['error'])) {
            $this->redirect('/dashboard');
            return;
        }

        $this->redirect('/assessment/result/' . $id); // PERUBAHAN
    }

    /**
     * Menampilkan hasil asesmen yang sudah selesai.
     * @param int $id ID upaya asesmen yang telah selesai
     */
    public function showResult(int $id) // PERUBAHAN
    {
        if (!Session::has('user')) {
            $this->redirect('/login');
            return;
        }

        $attemptResult = $this->assessmentService->getAttemptResultDetails($id); // PERUBAHAN
        
        if (!$attemptResult || $attemptResult['user_id'] !== Session::get('user')['id']) {
            $this->redirect('/dashboard');
            return;
        }

        $this->render('assessments/result', [
            'title' => 'Hasil ' . $attemptResult['assessment_title'],
            'attempt' => $attemptResult
        ]);
    }
}
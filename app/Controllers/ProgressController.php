<?php
namespace App\Controllers;

use App\Models\UserLessonProgress;
use App\Utils\Session;

class ProgressController extends BaseController
{
    private UserLessonProgress $userLessonProgressModel;

    public function __construct()
    {
        $this->userLessonProgressModel = new UserLessonProgress();
    }

    /**
     * Menandai pelajaran sebagai selesai.
     * Endpoint ini bisa dipanggil via AJAX/POST atau GET sederhana.
     */
    public function markLessonAsCompleted(int $lessonId)
    {
        if (!Session::has('user')) {
            $this->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            return;
        }

        $userId = Session::get('user')['id'];
        $success = $this->userLessonProgressModel->markAsCompleted($userId, $lessonId);

        if ($success) {
            $this->json(['status' => 'success', 'message' => 'Pelajaran ditandai selesai.'], 200);
        } else {
            $this->json(['status' => 'error', 'message' => 'Gagal menandai pelajaran.'], 500);
        }
    }

    // Method lain bisa ditambahkan di sini, misal untuk melihat progres user
    // public function showUserProgress() { ... }
}
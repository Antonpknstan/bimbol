<?php
namespace App\Controllers;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Course;
use App\Models\UserLessonProgress;
use App\Utils\Session;

class LessonController extends BaseController
{
    /**
     * Menampilkan satu halaman pelajaran.
     * @param int $id ID pelajaran
     */
    public function show(int $id)
    {
        $lessonModel = new Lesson();
        $lesson = $lessonModel->findById($id); // Kita perlu menambahkan method findById di Lesson Model

        if (!$lesson) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Pelajaran Tidak Ditemukan']);
            return;
        }

        // Ambil data lain untuk breadcrumbs
        $moduleModel = new Module();
        $courseModel = new Course();
        $module = $moduleModel->findById($lesson['module_id']);
        $course = $courseModel->findById($module['course_id']);

        // Cek status penyelesaian pelajaran oleh user yang login
        $isCompleted = false;
        if (Session::has('user')) {
            $progressModel = new UserLessonProgress();
            $progress = $progressModel->getProgress(Session::get('user')['id'], $id);
            if ($progress && $progress['is_completed']) {
                $isCompleted = true;
            }
        }

        $this->render('lessons/viewer', [
            'title' => $lesson['title'],
            'lesson' => $lesson,
            'module' => $module,
            'course' => $course,
            'isCompleted' => $isCompleted
        ]);
    }
}
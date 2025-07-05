<?php
namespace App\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;

class LearningController extends BaseController
{
    /**
     * Menampilkan halaman daftar semua kursus.
     */
    public function index()
    {
        $courseModel = new Course();
        $courses = $courseModel->findAllWithSubject();

        $this->render('courses/index', [
            'title' => 'Daftar Kursus',
            'courses' => $courses
        ]);
    }

    /**
     * Menampilkan halaman detail sebuah kursus.
     * @param int $id ID dari kursus
     */
    public function show(int $id)
    {
        $courseModel = new Course();
        $course = $courseModel->findById($id);

        if (!$course) {
            // Jika kursus tidak ditemukan, tampilkan halaman 404
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Kursus Tidak Ditemukan']);
            return;
        }

        // Ambil modul dan pelajaran dengan efisien
        $moduleModel = new Module();
        $lessonModel = new Lesson();
        
        $modules = $moduleModel->findByCourseId($id);
        $moduleIds = array_column($modules, 'module_id');
        $lessons = $lessonModel->findByModuleIds($moduleIds);

        // Petakan pelajaran ke modulnya masing-masing
        $lessonsByModule = [];
        foreach ($lessons as $lesson) {
            $lessonsByModule[$lesson['module_id']][] = $lesson;
        }

        // Tambahkan pelajaran ke dalam array modul
        foreach ($modules as &$module) {
            $module['lessons'] = $lessonsByModule[$module['module_id']] ?? [];
        }

        $this->render('courses/detail', [
            'title' => $course['title'],
            'course' => $course,
            'modules' => $modules
        ]);
    }
}
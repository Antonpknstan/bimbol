<?php
namespace App\Controllers;

class DashboardController extends BaseController {
    public function index() {
        $this->render('dashboard/index', ['title' => 'Dashboard']);
    }
}
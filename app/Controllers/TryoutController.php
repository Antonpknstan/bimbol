<?php
namespace App\Controllers;

use App\Models\TryoutPeriod;
use App\Models\Ranking;

class TryoutController extends BaseController
{
    public function index()
    {
        $tryoutModel = new TryoutPeriod();
        $tryouts = $tryoutModel->findAllActive();
        $this->render('tryouts/index', [
            'title' => 'Daftar Tryout',
            'tryouts' => $tryouts
        ]);
    }

    public function showLeaderboard(int $id)
    {
        $tryoutModel = new TryoutPeriod();
        $rankingModel = new Ranking();
        
        $tryout = $tryoutModel->findById($id);
        if (!$tryout) {
            http_response_code(404);
            return $this->render('errors/404');
        }

        $leaderboard = $rankingModel->getLeaderboard($id);

        $this->render('tryouts/leaderboard', [
            'title' => 'Peringkat Tryout: ' . htmlspecialchars($tryout['name']),
            'leaderboard' => $leaderboard
        ]);
    }
}
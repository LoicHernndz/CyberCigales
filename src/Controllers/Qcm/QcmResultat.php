<?php

namespace Controllers\Qcm;

use Controllers\AbstractController;
use Models\QcmQuestion;
use Models\UserQcmProgress;
use Views\Qcm\QcmResultat\QcmResultatView;

/**
 * Contrôleur pour afficher les résultats finaux du QCM
 * Route: GET /qcm/rgpd/resultat
 */
class QcmResultat extends AbstractController
{
    private QcmQuestion $questionModel;
    private UserQcmProgress $progressModel;

    public function __construct()
    {
        $this->questionModel = new QcmQuestion();
        $this->progressModel = new UserQcmProgress();
    }

    function getMethod()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('/user/login');
        }

        $userId = $_SESSION['user_id'];

        // Récupérer les statistiques
        $totalQuestions = $this->questionModel->getTotalQuestions();
        $totalAnswered = $this->progressModel->countTotalAnswers($userId);
        $correctAnswers = $this->progressModel->countCorrectAnswers($userId);
        $score = $this->progressModel->getUserScore($userId);
        $successRate = $this->progressModel->getSuccessRate($userId);

        // Récupérer le classement
        $leaderboard = $this->progressModel->getLeaderboard(10);

        // Afficher la vue
        $view = new QcmResultatView();
        $view->setStatistics([
            'total_questions' => $totalQuestions,
            'total_answered' => $totalAnswered,
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'success_rate' => $successRate
        ]);
        $view->setLeaderboard($leaderboard);
        $view->render();
    }
}


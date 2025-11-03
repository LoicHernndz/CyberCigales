<?php

namespace Controllers\Qcm;

use Controllers\AbstractController;
use Models\QcmQuestion;
use Views\Qcm\QcmFeedback\QcmFeedbackView;

/**
 * Contrôleur pour afficher le feedback après une réponse
 * Route: GET /qcm/rgpd/feedback
 */
class QcmFeedback extends AbstractController
{
    private QcmQuestion $questionModel;

    public function __construct()
    {
        $this->questionModel = new QcmQuestion();
    }

    function getMethod()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('/user/login');
        }

        // Récupérer les données de feedback
        if (!isset($_SESSION['qcm_feedback'])) {
            redirect('/qcm/rgpd');
        }

        $feedback = $_SESSION['qcm_feedback'];
        
        // Calculer le numéro de la prochaine question
        $nextQuestionNum = $feedback['question_num'] + 1;
        $totalQuestions = $this->questionModel->getTotalQuestions();

        // Afficher la vue
        $view = new QcmFeedbackView();
        $view->setFeedback($feedback);
        $view->setNextQuestionNum($nextQuestionNum);
        $view->setTotalQuestions($totalQuestions);
        $view->render();

        // Supprimer les données de feedback de la session
        unset($_SESSION['qcm_feedback']);
    }
}


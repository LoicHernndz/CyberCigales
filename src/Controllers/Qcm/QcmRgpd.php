<?php

namespace Controllers\Qcm;

use Controllers\AbstractController;
use Models\QcmQuestion;
use Models\User\User;
use Models\UserQcmProgress;
use Views\Qcm\QcmRgpd\QcmRgpdView;

/**
 * Contrôleur pour afficher le QCM RGPD
 * Route: GET /qcm/rgpd
 */
class QcmRgpd extends AbstractController
{
    private QcmQuestion $questionModel;
    private UserQcmProgress $progressModel;
    private User $userModel;

    public function __construct()
    {
        $this->questionModel = new QcmQuestion();
        $this->progressModel = new UserQcmProgress();
    }

    /**
     * Affiche le QCM RGPD
     */
    function getMethod()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('qcm', 'Vous devez être connecté pour accéder au QCM.', 'form-message form-message-red');
            redirect('/user/login');
        }

        $userId = $_SESSION['user_id'];

        // Récupérer le numéro de question (par défaut : 1)
        $questionNum = isset($_GET['q']) ? (int)$_GET['q'] : 1;

        // Vérifier que le numéro est valide
        $totalQuestions = $this->questionModel->getTotalQuestions();
        if ($questionNum < 1) {
            $questionNum = 1;
        } elseif ($questionNum > $totalQuestions) {
            // Si toutes les questions sont terminées, rediriger vers les résultats
            redirect('/qcm/rgpd/resultat');
        }

        // Récupérer la question actuelle
        $question = $this->questionModel->getQuestionByOrdre($questionNum);

        if (!$question) {
            echo "Erreur : Question non trouvée.";
            exit();
        }

        // Vérifier si l'utilisateur a déjà répondu à cette question
        $dejaRepondu = $this->progressModel->hasAnswered($userId, $question->id);

        // Récupérer la progression
        $totalAnswered = $this->progressModel->countTotalAnswers($userId);
        $score = $this->progressModel->getUserScore($userId);

        // Afficher la vue
        $view = new QcmRgpdView();
        $view->setQuestion($question);
        $view->setQuestionNum($questionNum);
        $view->setTotalQuestions($totalQuestions);
        $view->setScore($score);
        $view->setProgress($totalAnswered);
        $view->setDejaRepondu($dejaRepondu);
        $view->render();
    }

    function postMethod()
    {
        $this->userModel = new User();

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('qcm', 'Vous devez être connecté pour répondre au QCM.', 'form-message form-message-red');
            redirect('/user/login');
        }

        $userId = $_SESSION['user_id'];

        // Récupérer les données POST
        $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $reponseId = isset($_POST['reponse_id']) ? (int)$_POST['reponse_id'] : 0;
        $questionNum = isset($_POST['question_num']) ? (int)$_POST['question_num'] : 1;

        if (empty($reponseId)) {
            flash('qcm_temp', 'Veuillez sélectionner une réponse.', 'form-message form-message-red');
            redirect('/qcm/rgpd?q=' . $questionNum);
        }

        // Récupérer l'ID de la question depuis la réponse
        $questionId = $this->questionModel->getQuestionIdFromReponse($reponseId);

        if (!$questionId) {
            flash('qcm_temp', 'Erreur : réponse invalide.', 'form-message form-message-red');
            redirect('/qcm/rgpd?q=' . $questionNum);
        }

        // Vérifier si la réponse est correcte
        $estCorrect = $this->questionModel->isReponseCorrect($reponseId);

        // Calculer les points
        $points = $this->questionModel->getPoints($questionId, $estCorrect);

        // Sauvegarder la réponse
        $this->progressModel->saveReponse($userId, $questionId, $reponseId, $estCorrect, $points);

        // Mettre à jour le score total de l'utilisateur
        $this->userModel->ajouterScore($userId, $points);

        // Récupérer l'explication
        $explication = $this->questionModel->getExplication($questionId);

        // Stocker les infos pour la page de feedback
        $_SESSION['qcm_feedback'] = [
            'est_correct' => $estCorrect,
            'points' => $points,
            'explication' => $explication,
            'question_num' => $questionNum
        ];

        // Rediriger vers la page de feedback
        redirect('/qcm/rgpd/feedback');
    }
}


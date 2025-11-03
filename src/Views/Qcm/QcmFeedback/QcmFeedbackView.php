<?php

namespace Views\Qcm\QcmFeedback;

use Views\AbstractView;

class QcmFeedbackView extends AbstractView
{
    private $feedback;
    private $nextQuestionNum;
    private $totalQuestions;

    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    }

    public function setNextQuestionNum($nextQuestionNum)
    {
        $this->nextQuestionNum = $nextQuestionNum;
    }

    public function setTotalQuestions($totalQuestions)
    {
        $this->totalQuestions = $totalQuestions;
    }

    function templatePath(): string
    {
        return __DIR__ . '/qcm-feedback.html';
    }

    function templateKeys(): array
    {
        $isCorrect = $this->feedback['est_correct'];
        $points = $this->feedback['points'];
        $explication = htmlspecialchars($this->feedback['explication']);

        $icon = $isCorrect ? '✓' : '✗';
        $title = $isCorrect ? 'Bravo ! Bonne réponse !' : 'Mauvaise réponse';
        $class = $isCorrect ? 'feedback-success' : 'feedback-error';
        $pointsText = $points >= 0 ? '+' . $points : $points;
        $pointsClass = $points >= 0 ? 'points-positive' : 'points-negative';

        $nextButton = '';
        if ($this->nextQuestionNum <= $this->totalQuestions) {
            $nextButton = '<a href="/qcm/rgpd?q=' . $this->nextQuestionNum . '" class="btn-next">
                Question suivante →
            </a>';
        } else {
            $nextButton = '<a href="/qcm/rgpd/resultat" class="btn-next">
                Voir mes résultats finaux 📊
            </a>';
        }

        return [
            'ICON' => $icon,
            'TITLE' => $title,
            'CLASS' => $class,
            'POINTS' => $pointsText,
            'POINTS_CLASS' => $pointsClass,
            'EXPLICATION' => $explication,
            'NEXT_BUTTON' => $nextButton
        ];
    }
}


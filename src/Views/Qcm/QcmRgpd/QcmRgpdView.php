<?php

namespace Views\Qcm\QcmRgpd;

use Views\AbstractView;

/**
 * Vue pour le QCM RGPD
 */
class QcmRgpdView extends AbstractView
{
    private $question;
    private $questionNum;
    private $totalQuestions;
    private $score;
    private $progress;
    private $dejaRepondu;

    public function setQuestion($question)
    {
        $this->question = $question;
    }

    public function setQuestionNum($questionNum)
    {
        $this->questionNum = $questionNum;
    }

    public function setTotalQuestions($totalQuestions)
    {
        $this->totalQuestions = $totalQuestions;
    }

    public function setScore($score)
    {
        $this->score = $score;
    }

    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    public function setDejaRepondu($dejaRepondu)
    {
        $this->dejaRepondu = $dejaRepondu;
    }

    function templatePath(): string
    {
        return __DIR__ . '/qcm-rgpd.html';
    }

    function templateKeys(): array
    {
        $questionText = htmlspecialchars($this->question->question);
        $progressPercent = round(($this->progress / $this->totalQuestions) * 100);
        $categorieBadge = $this->getCategorieLabel($this->question->categorie);
        $difficulteColor = $this->getDifficulteColor($this->question->difficulte);

        // Générer les boutons de réponses
        $reponsesHtml = '';
        foreach ($this->question->reponses as $index => $reponse) {
            $letter = chr(65 + $index); // A, B, C, D
            $reponsesHtml .= '<label class="reponse-card" for="reponse_' . $reponse->id . '">
                <input type="radio" name="reponse_id" id="reponse_' . $reponse->id . '" 
                       value="' . $reponse->id . '" required>
                <span class="reponse-letter">' . $letter . '</span>
                <span class="reponse-text">' . htmlspecialchars($reponse->reponse) . '</span>
            </label>';
        }

        // Message si déjà répondu
        $dejaReponduMsg = '';
        if ($this->dejaRepondu) {
            $dejaReponduMsg = '<div class="info-banner">
                ℹ️ Vous avez déjà répondu à cette question. Vous pouvez changer votre réponse.
            </div>';
        }

        return [
            'QUESTION_NUM' => $this->questionNum,
            'TOTAL_QUESTIONS' => $this->totalQuestions,
            'QUESTION' => $questionText,
            'CATEGORIE_BADGE' => $categorieBadge,
            'DIFFICULTE' => ucfirst($this->question->difficulte),
            'DIFFICULTE_COLOR' => $difficulteColor,
            'POINTS_CORRECT' => $this->question->points_correct,
            'POINTS_INCORRECT' => $this->question->points_incorrect,
            'SCORE' => $this->score,
            'PROGRESS_PERCENT' => $progressPercent,
            'REPONSES' => $reponsesHtml,
            'DEJA_REPONDU_MSG' => $dejaReponduMsg,
            'FLASH_MESSAGE' => flash('qcm_temp')
        ];
    }

    private function getCategorieLabel($categorie): string
    {
        $labels = [
            'general' => 'Général',
            'droits' => 'Vos Droits',
            'obligations' => 'Obligations',
            'sanctions' => 'Sanctions',
            'cas_pratique' => 'Cas Pratique'
        ];
        return $labels[$categorie] ?? 'Général';
    }

    private function getDifficulteColor($difficulte): string
    {
        $colors = [
            'facile' => '#00ff41',
            'moyen' => '#ffa500',
            'difficile' => '#ff4444'
        ];
        return $colors[$difficulte] ?? '#ffa500';
    }
}


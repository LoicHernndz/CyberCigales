<?php

namespace Views\Qcm\QcmResultat;

use Views\AbstractView;

class QcmResultatView extends AbstractView
{
    private $statistics;
    private $leaderboard;

    public function setStatistics($statistics)
    {
        $this->statistics = $statistics;
    }

    public function setLeaderboard($leaderboard)
    {
        $this->leaderboard = $leaderboard;
    }

    function templatePath(): string
    {
        return __DIR__ . '/qcm-resultat.html';
    }

    function templateKeys(): array
    {
        $stats = $this->statistics;
        
        // Déterminer le niveau
        $niveau = $this->getNiveau($stats['success_rate']);
        
        // Générer le classement HTML
        $leaderboardHtml = '';
        foreach ($this->leaderboard as $index => $entry) {
            $rank = $index + 1;
            $medal = $this->getMedal($rank);
            $leaderboardHtml .= '<tr>
                <td>' . $medal . ' ' . $rank . '</td>
                <td>' . htmlspecialchars($entry->pseudo) . '</td>
                <td>' . $entry->bonnes_reponses . '/' . $entry->total_reponses . '</td>
                <td class="score-cell">' . $entry->total_score . ' pts</td>
            </tr>';
        }

        return [
            'TOTAL_QUESTIONS' => $stats['total_questions'],
            'TOTAL_ANSWERED' => $stats['total_answered'],
            'CORRECT_ANSWERS' => $stats['correct_answers'],
            'SCORE' => $stats['score'],
            'SUCCESS_RATE' => $stats['success_rate'],
            'NIVEAU' => $niveau['label'],
            'NIVEAU_COLOR' => $niveau['color'],
            'COMMENTAIRE' => $niveau['commentaire'],
            'LEADERBOARD' => $leaderboardHtml
        ];
    }

    private function getNiveau($successRate): array
    {
        if ($successRate >= 90) {
            return [
                'emoji' => '',
                'label' => 'Expert RGPD',
                'color' => '#FFD700',
                'commentaire' => 'Impressionnant ! Tu maîtrises le RGPD comme un pro !'
            ];
        } elseif ($successRate >= 75) {
            return [
                'emoji' => '',
                'label' => 'Champion de la protection des données',
                'color' => '#00ff41',
                'commentaire' => 'Excellent ! Tu as de solides connaissances en RGPD.'
            ];
        } elseif ($successRate >= 60) {
            return [
                'emoji' => '',
                'label' => 'Bon élève du RGPD',
                'color' => '#ffa500',
                'commentaire' => 'Bien joué ! Continue comme ça, tu progresses !'
            ];
        } elseif ($successRate >= 40) {
            return [
                'emoji' => '',
                'label' => 'Apprenti de la protection des données',
                'color' => '#88ccff',
                'commentaire' => 'C\'est un bon début ! Encore quelques révisions et ce sera parfait.'
            ];
        } else {
            return [
                'emoji' => '',
                'label' => 'Débutant RGPD',
                'color' => '#ff4444',
                'commentaire' => 'Pas de panique ! Reprends le QCM pour apprendre davantage.'
            ];
        }
    }

    private function getMedal($rank): string
    {
        return match($rank) {
            1 => '#1',
            2 => '#2',
            3 => '#3',
            default => ''
        };
    }
}


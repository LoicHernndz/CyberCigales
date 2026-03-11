<?php
namespace Controllers;

use Models\User\UserStats;
use Models\Lesson\LessonProgress;
use Views\Homepage\HomepageView;
use Attributes\Route;

#[Route('/', name: 'homepage')]
class Homepage extends AbstractController
{
    function getMethod(){
        $view = new HomepageView();

        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $view->addTemplateKey('USERNAME_KEY', explode(" ", $_SESSION['user_pseudo'] ?? '')[0]);

            // Stats utilisateur
            try {
                $userStats = new UserStats();
                $stats = $userStats->getUserStats($userId);

                $modulesCompleted = 0;
                if (isset($stats['rgpd']['completion']) && $stats['rgpd']['completion'] >= 100) $modulesCompleted++;
                if (isset($stats['cypher']['games_played']) && $stats['cypher']['games_played'] > 0) $modulesCompleted++;

                $view->addTemplateKey('MODULES_COMPLETED', $modulesCompleted);
                $view->addTemplateKey('TOTAL_POINTS', $stats['general']['total_score'] ?? 0);

                $learningTime = 0;
                $totalTime = $stats['general']['total_time'] ?? '';
                if (preg_match('/(\d+)h/', $totalTime, $h)) $learningTime += $h[1] * 60;
                if (preg_match('/(\d+)min/', $totalTime, $m)) $learningTime += $m[1];
                $view->addTemplateKey('LEARNING_TIME', $learningTime);
                $view->addTemplateKey('CYPHER_COMPLETION', (isset($stats['cypher']['games_played']) && $stats['cypher']['games_played'] > 0) ? 100 : 0);

                $badges = $userStats->getBadges($userId);
                $view->addTemplateKey('UNLOCKED_BADGES', count(array_filter($badges, fn($b) => $b['unlocked'])));
            } catch (\Throwable $e) {
                error_log('Homepage stats error: ' . $e->getMessage());
            }

            // Escape game : verrouillé ou débloqué
            $allDone = false;
            try {
                $lessonProgress = new LessonProgress();
                $allDone = $lessonProgress->areRequiredLessonsCompleted($userId);
            } catch (\Throwable $e) {
                error_log('Homepage lesson error: ' . $e->getMessage());
            }

            if ($allDone) {
                $view->addTemplateKey('ESCAPE_CARD', '<a href="' . url('macos') . '" class="concept-card">
                    <div class="concept-icon"><span class="material-icons">sports_esports</span></div>
                    <h3>Notre Escape Game</h3>
                    <p>Mettez en pratique toutes les connaissances que vous avez au travers de cet escape game interactif et ludique.</p>
                    <span class="btn-card-action"><span>Lancer l\'escape game</span><span class="material-icons">play_arrow</span></span>
                </a>');
            } else {
                $view->addTemplateKey('ESCAPE_CARD', '<div class="concept-card escape-locked">
                    <div class="escape-lock-overlay"><span class="material-icons">lock</span></div>
                    <div class="concept-icon"><span class="material-icons">sports_esports</span></div>
                    <h3>Notre Escape Game</h3>
                    <p>Terminez les 3 leçons obligatoires pour débloquer l\'escape game.</p>
                    <span class="btn-card-action btn-card-disabled"><span>Verrouillé</span><span class="material-icons">lock</span></span>
                </div>');
            }
        }

        $view->render();
    }
}

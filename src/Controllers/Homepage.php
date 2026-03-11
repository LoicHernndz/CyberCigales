<?php
namespace Controllers;

use Models\User\User;
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

            // Statistiques utilisateur (indépendant des leçons)
            try {
                $userStats = new UserStats();
                $stats = $userStats->getUserStats($userId);
                $this->updateUserStatsInView($view, $stats, $userId, $userStats);
            } catch (\Exception $e) {
                error_log('Homepage UserStats error: ' . $e->getMessage());
            }

            // Progression des leçons (indépendant des stats)
            $this->updateLessonProgressInView($view, $userId);
        }

        $view->render();
    }

    private function updateLessonProgressInView($view, $userId): void {
        try {
            $lessonProgress = new LessonProgress();
            $completedLessons = $lessonProgress->getCompletedLessons($userId);
            $lessonsCount = count(array_intersect($completedLessons, ['cesar', 'vigenere', 'permutation']));
            $allDone = $lessonsCount >= 3;

            $view->addTemplateKey('LESSONS_DONE_COUNT', $lessonsCount);
            $view->addTemplateKey('LESSONS_PERCENT', round($lessonsCount / 3 * 100));

            foreach (['cesar', 'vigenere', 'permutation'] as $lesson) {
                $done = in_array($lesson, $completedLessons);
                $key = strtoupper($lesson);
                $view->addTemplateKey($key . '_DONE_CLASS', $done ? 'prereq-done' : 'prereq-todo');
                $view->addTemplateKey($key . '_DONE_ICON', $done ? 'check_circle' : 'radio_button_unchecked');
            }

            if ($allDone) {
                $escapeCard = '<a href="' . url('macos') . '" class="concept-card">
                        <div class="concept-icon"><span class="material-icons">sports_esports</span></div>
                        <h3>Notre Escape Game</h3>
                        <p>Mettez en pratique toutes les connaissances que vous avez au travers de cet escape game interactif et ludique.</p>
                        <span class="btn-card-action"><span>Lancer l\'escape game</span><span class="material-icons">play_arrow</span></span>
                    </a>';
            } else {
                $escapeCard = '<div class="concept-card escape-locked">
                        <div class="escape-lock-overlay"><span class="material-icons">lock</span></div>
                        <div class="concept-icon"><span class="material-icons">sports_esports</span></div>
                        <h3>Notre Escape Game</h3>
                        <p>Terminez les 3 leçons obligatoires pour débloquer l\'escape game.</p>
                        <span class="btn-card-action btn-card-disabled"><span>Verrouillé — ' . $lessonsCount . '/3 leçons</span><span class="material-icons">lock</span></span>
                    </div>';
            }
            $view->addTemplateKey('ESCAPE_CARD', $escapeCard);
        } catch (\Exception $e) {
            error_log('Homepage LessonProgress error: ' . $e->getMessage());
        }
    }

    private function updateUserStatsInView($view, $stats, $userId, UserStats $userStats) {
        $modulesCompleted = 0;
        if (isset($stats['rgpd']) && $stats['rgpd']['completion'] >= 100) $modulesCompleted++;
        if (isset($stats['cypher']['games_played']) && $stats['cypher']['games_played'] > 0) $modulesCompleted++;

        $totalPoints = $stats['general']['total_score'] ?? 0;

        $learningTime = 0;
        $totalTime = $stats['general']['total_time'] ?? '';
        if (preg_match('/(\d+)h/', $totalTime, $hours)) {
            $learningTime += $hours[1] * 60;
        }
        if (preg_match('/(\d+)min/', $totalTime, $minutes)) {
            $learningTime += $minutes[1];
        }

        $rgpdCompletion = 0;
        $cypherCompletion = (isset($stats['cypher']['games_played']) && $stats['cypher']['games_played'] > 0) ? 100 : 0;

        $badges = $userStats->getBadges($userId);
        $unlockedBadges = array_filter($badges, function($badge) {
            return $badge['unlocked'];
        });

        $view->addTemplateKey('MODULES_COMPLETED', $modulesCompleted);
        $view->addTemplateKey('TOTAL_POINTS', $totalPoints);
        $view->addTemplateKey('LEARNING_TIME', $learningTime);
        $view->addTemplateKey('RGPD_COMPLETION', $rgpdCompletion);
        $view->addTemplateKey('CYPHER_COMPLETION', $cypherCompletion);
        $view->addTemplateKey('UNLOCKED_BADGES', count($unlockedBadges));
    }
}

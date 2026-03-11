<?php
namespace Controllers;

use Models\User\User;
use Models\User\UserStats;
use Models\Lesson\LessonProgress;
use Views\Homepage\HomepageView;
use Attributes\Route;

/**
 * Contrôleur de la page d'accueil
 * 
 * Affiche la page d'accueil avec statistiques personnalisées pour les utilisateurs connectés.
 */
#[Route('/', name: 'homepage')]
class Homepage extends AbstractController
{

    /**
     * Affiche la page d'accueil
     * 
     * Récupère et affiche les statistiques de l'utilisateur s'il est connecté.
     * 
     * @return void
     */
    function getMethod(){
        // Création d'une instance de la vue "HomepageView"
        $view = new HomepageView();
        
        // Si un utilisateur est connecté, on récupère ses statistiques
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            
            // Récupération des statistiques de l'utilisateur
            $userStats = new UserStats();
            $stats = $userStats->getUserStats($userId);
            
            $this->updateUserStatsInView($view, $stats, $userId, $userStats);
        }
        
        // Affichage de la page d'accueil
        $view->render();
    }
    
    // Méthode pour mettre à jour les statistiques dans la vue
    private function updateUserStatsInView($view, $stats, $userId, UserStats $userStats) {
        // Modules complétés
        $modulesCompleted = 0;
        if (isset($stats['rgpd']) && $stats['rgpd']['completion'] >= 100) $modulesCompleted++;
        if ($stats['cypher']['games_played'] > 0) $modulesCompleted++;
        
        // Points gagnés
        $totalPoints = $stats['general']['total_score'];
        
        // Temps d'apprentissage en minutes
        $learningTime = 0;
        if (preg_match('/(\d+)h/', $stats['general']['total_time'], $hours)) {
            $learningTime += $hours[1] * 60;
        }
        if (preg_match('/(\d+)min/', $stats['general']['total_time'], $minutes)) {
            $learningTime += $minutes[1];
        }
        
        // RGPD progression (mise à 0 car supprimé)
        $rgpdCompletion = 0;
        
        // Cypher Rush progression
        $cypherCompletion = $stats['cypher']['games_played'] > 0 ? 100 : 0;
        
        // Badges débloqués
        $badges = $userStats->getBadges($userId);
        $unlockedBadges = array_filter($badges, function($badge) {
            return $badge['unlocked'];
        });
        
        // Mise à jour des variables dans la vue
        $view->addTemplateKey('MODULES_COMPLETED', $modulesCompleted);
        $view->addTemplateKey('TOTAL_POINTS', $totalPoints);
        $view->addTemplateKey('LEARNING_TIME', $learningTime);
        $view->addTemplateKey('RGPD_COMPLETION', $rgpdCompletion);
        $view->addTemplateKey('CYPHER_COMPLETION', $cypherCompletion);
        $view->addTemplateKey('UNLOCKED_BADGES', count($unlockedBadges));

        // Progression des leçons pour l'escape game
        try {
            $lessonProgress = new LessonProgress();
            $completedLessons = $lessonProgress->getCompletedLessons($userId);
            $lessonsCount = count(array_intersect($completedLessons, ['cesar', 'vigenere', 'permutation']));
            $allDone = $lessonsCount >= 3;

            error_log('Homepage lesson progress: userId=' . $userId . ' completed=' . json_encode($completedLessons) . ' count=' . $lessonsCount);

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
            error_log('Homepage lesson progress ERROR: ' . $e->getMessage());
        }
    }
}

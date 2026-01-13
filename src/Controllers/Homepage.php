<?php
namespace Controllers;

use Models\User\User;
use Models\User\UserStats;
use Views\Homepage\HomepageView;

/**
 * Contrôleur de la page d'accueil
 * 
 * Affiche la page d'accueil avec statistiques personnalisées pour les utilisateurs connectés.
 */
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
    }

    // Méthode statique permettant de déterminer si ce contrôleur doit être utilisé
    static function support(string $chemin, string $method) : bool{
        // Ce contrôleur s'active uniquement si :
        // - l'URL demandée est "/"
        // - la méthode HTTP utilisée est "GET"
        return $chemin === "/" && $method === "GET";
    }
}

<?php
namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\UserStats;
use Views\User\Profil\ProfilView;
use Attributes\Route;

/**
 * Contrôleur d'affichage du profil utilisateur
 *
 * Affiche les statistiques de jeux, badges obtenus et classement de l'utilisateur connecté.
 */
#[Route('/user/profil', name: 'user_profil')]
class Profil extends AbstractController
{
    /**
     * Affiche la page de profil avec les statistiques utilisateur
     * 
     * Récupère et affiche les stats de jeux, badges et rang de l'utilisateur.
     * Redirige vers login si l'utilisateur n'est pas connecté.
     * 
     * @return void
     */
    function getMethod(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('profil', 'Vous devez être connecté pour accéder à votre profil.', 'form-message form-message-red');
            redirect(url('user_login'));
            return;
        }

        // Récupérer l'ID de l'utilisateur
        $userId = $_SESSION['user_id'];

        // Créer une instance du modèle UserStats
        $userStatsModel = new UserStats();

        // Récupérer toutes les statistiques
        $stats = $userStatsModel->getUserStats($userId);
        $badges = $userStatsModel->getBadges($userId);
        $rank = $userStatsModel->getUserRank($userId);

        // Mettre à jour les variables de session avec les stats récentes
        if (!empty($stats['general'])) {
            $_SESSION['user_total_score'] = $stats['general']['total_score'];
            $_SESSION['user_time'] = $stats['general']['total_time'];
        }

        // Créer une instance de la vue et passer les données
        $view = new ProfilView($stats, $badges, $rank);
        
        // Afficher le contenu de la page de profil
        $view->render();
    }
}

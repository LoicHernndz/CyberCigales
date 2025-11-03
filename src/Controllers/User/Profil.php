<?php
namespace Controllers\User;

use Controllers\AbstractController;
use Models\User\UserStats;
use Views\User\Profil\ProfilView;

class Profil extends AbstractController
{
    // Méthode principale exécutée lorsque la route "/user/profil" est appelée en GET
    function getMethod(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('profil', 'Vous devez être connecté pour accéder à votre profil.', 'form-message form-message-red');
            redirect('/user/login');
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

    // Méthode permettant de déterminer si ce contrôleur doit être utilisé
    static function support(string $chemin, string $method) : bool{
        // Ce contrôleur s'active uniquement si :
        // - le chemin de l'URL est "/user/profil"
        // - la méthode HTTP utilisée est "GET"
        return $chemin === "/user/profil" && $method === "GET";
    }
}

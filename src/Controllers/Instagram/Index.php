<?php
namespace Controllers\Instagram;

use Views\Instagram\InstagramView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

/**
 * Contrôleur pour la page d'accueil Instagram
 * 
 * Ce contrôleur gère l'affichage du feed Instagram principal avec :
 * - Les stories (avatars cliquables)
 * - Les posts du feed avec likes, commentaires, etc.
 * 
 * Architecture MVC :
 * - Modèle : Données statiques (stories, posts)
 * - Vue : InstagramView (template HTML)
 * - Contrôleur : Instagram (logique métier)
 */
class Instagram extends AbstractController
{
    /**
     * Méthode principale appelée lors de l'accès à /instagram
     * Génère les données et les passe à la vue pour affichage
     */
    function getMethod(){

        // Vérifier si l'utilisateur est connecté
        $this->connexionVerify();

        // Création du model
        $model = new InstagramModel();

        // ========================================
        // RÉCUPÉRATION DES DONNÉES VIA LE MODÈLE
        // ========================================
        // Le modèle gère toutes les données statiques et dynamiques
        $stories = $model->getStories();

        // Récupération des posts via le modèle
        $posts = $model->getPosts();

        // Affichage final de la view Instagram
        $view = new InstagramView($stories, $posts);
        $view->render();
    }
    
    /**
     * Méthode statique permettant de déterminer si ce contrôleur doit être utilisé
     * 
     * @param string $chemin L'URL demandée
     * @param string $method La méthode HTTP (GET, POST, etc.)
     * @return bool True si ce contrôleur doit gérer cette requête
     */
    static function support(string $chemin, string $method) : bool{
        // Ce contrôleur s'active uniquement si :
        // - l'URL demandée est "/instagram"
        // - la méthode HTTP utilisée est "GET"
        return $chemin === "/instagram" && $method === "GET";
    }
}

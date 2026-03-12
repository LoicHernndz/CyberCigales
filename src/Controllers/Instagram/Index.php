<?php
namespace Controllers\Instagram;

use Controllers\AbstractController;
use Models\Instagram\InstagramModel;
use Views\Instagram\InstagramView;
use Attributes\Route;

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
#[Route('/instagram', name: 'instagram')]
class Index extends AbstractController
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
}

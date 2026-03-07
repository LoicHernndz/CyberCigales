<?php

namespace Controllers\Instagram;

use Views\Instagram\UserProfileView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;
use Attributes\Route;

/**
 * Contrôleur pour afficher le profil d'un utilisateur Instagram
 * 
 * Clés transmises à la vue :
 * - USERNAME : Nom d'utilisateur (@username)
 * - DISPLAY_NAME : Nom affiché
 * - AVATAR : URL de l'avatar
 * - POSTS_COUNT, FOLLOWERS_COUNT, FOLLOWING_COUNT : Statistiques du profil
 * - VERIFIED_ICON : Icône de vérification si compte vérifié
 * - PROFILE_INFO : HTML des informations du profil (bio, site web)
 * - POSTS : HTML de la grille de posts
 * - CHAT_URL : Lien vers le chat
 */
#[Route('/instagram/user-profile', name: 'instagram_user_profile')]
class UserProfile extends AbstractController
{
    /**
     * Affiche le profil d'un utilisateur Instagram
     * 
     * Récupère le username depuis l'URL et charge les données du profil.
     * 
     * @return void
     */
    public function getMethod(): void
    {

        // Vérifier si l'utilisateur est connecté
        $this->connexionVerify();

        // Récupérer le username depuis l'URL
        $uri = $_SERVER['REQUEST_URI'];
        $parts = explode('/', trim($uri, '/'));
        $username = end($parts);

        // Supprimer les paramètres GET si présents
        if (strpos($username, '?') !== false) {
            $username = explode('?', $username)[0];
        }

        // Création des instances MVC
        $view = new UserProfileView();
        $model = new InstagramModel();

        // Récupération du profil
        $profileData = $model->getUserProfile($username);

        // Si le profil n'existe pas, rediriger vers la page Instagram
        if ($profileData === null) {
            header('Location: /instagram');
            exit;
        }

        // Passage des données brutes à la vue (pas de HTML dans le controller)
        $view->setProfileData($profileData);
        $view->addTemplateKey('CHAT_URL', '/instagram/user/' . urlencode($username) . '/chat');

        $view->render();
    }
}

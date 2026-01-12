<?php

namespace Controllers\Instagram;

use Views\Instagram\UserProfileView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

/**
 * Contrôleur pour afficher le profil d'un utilisateur Instagram générique
 */
class UserProfile extends AbstractController
{
    public function getMethod(): void
    {
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
        
        // Génération du HTML pour les posts
        $postsHtml = '';
        foreach($profileData['posts'] as $post) {
            $videoIcon = isset($post['is_video']) && $post['is_video'] 
                ? '<img src="/images/instagram/svgs/videocam-outline.svg" alt="Vidéo" class="ionicon video_icon">' 
                : '';
            
            $postsHtml .= '
            <figure class="figure">
                ' . $videoIcon . '
                <img loading="lazy" src="' . $post['image'] . '" alt="Post ' . $post['id'] . '" />
                <figcaption class="access-hidden">Post</figcaption>
            </figure>';
        }
        
        // Génération du HTML pour les informations du profil
        $profileInfoHtml = '
        <div class="profile-info">
            <h2 class="profile-name">' . htmlspecialchars($profileData['display_name']) . '</h2>
            <div class="profile-bio">
                <p>' . nl2br(htmlspecialchars($profileData['bio'])) . '</p>';
        
        if (!empty($profileData['website'])) {
            $profileInfoHtml .= '
                <p>
                    <a href="#" class="profile-link">
                        <img src="/images/instagram/svgs/link-outline.svg" alt="Link" class="icon" style="width: 16px; height: 16px;">
                        <span>' . htmlspecialchars($profileData['website']) . '</span>
                    </a>
                </p>';
        }
        
        $profileInfoHtml .= '
            </div>
        </div>';

        // Icône de vérification
        $verifiedIcon = $profileData['verified'] 
            ? '<img src="/images/instagram/svgs/shield-checkmark.svg" alt="Vérifié" class="ionicon verified">' 
            : '';

        // Passage des données à la vue
        $view->addTemplateKey('USERNAME', htmlspecialchars($profileData['username']));
        $view->addTemplateKey('DISPLAY_NAME', htmlspecialchars($profileData['display_name']));
        $view->addTemplateKey('AVATAR', htmlspecialchars($profileData['avatar']));
        $view->addTemplateKey('POSTS_COUNT', htmlspecialchars($profileData['posts_count']));
        $view->addTemplateKey('FOLLOWERS_COUNT', htmlspecialchars($profileData['followers_count']));
        $view->addTemplateKey('FOLLOWING_COUNT', htmlspecialchars($profileData['following_count']));
        $view->addTemplateKey('VERIFIED_ICON', $verifiedIcon);
        $view->addTemplateKey('PROFILE_INFO', $profileInfoHtml);
        $view->addTemplateKey('POSTS', $postsHtml);
        $view->addTemplateKey('CHAT_URL', '/instagram/user/' . urlencode($username) . '/chat');
        
        $view->render();
    }
    
    public function support(string $method): bool
    {
        return $method === 'GET';
    }
}


<?php

namespace Controllers\Instagram;

use Views\Instagram\MelinaProfileView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

class MelinaProfile extends AbstractController
{
    public function getMethod(): void
    {
        // Création des instances MVC
        $view = new MelinaProfileView();
        $model = new InstagramModel();
        
        // ========================================
        // RÉCUPÉRATION DES DONNÉES VIA LE MODÈLE
        // ========================================
        $profileData = $model->getMelinaProfile();
        
        // Récupération des posts via le modèle
        $profilePosts = $model->getMelinaPosts();
        
        // Génération du HTML pour les posts
        $postsHtml = '';
        foreach($profilePosts as $post) {
            $pinnedIcon = '';
            
            $videoIcon = $post['is_video'] ? '<img src="/images/instagram/svgs/videocam-outline.svg" alt="Vidéo" class="ionicon video_icon">' : '';
            
            $postsHtml .= '
            <figure class="figure">
                ' . $pinnedIcon . $videoIcon . '
                <img loading="lazy" src="' . $post['image'] . '" alt="Post ' . $post['id'] . '" />
                <figcaption class="access-hidden">' . ($post['type'] === 'pinned' ? 'Pinned Post' : 'Post') . '</figcaption>
            </figure>';
        }
        
        // Génération du HTML pour les données du profil (avatar + stats)
        $profileDataHtml = '
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="' . $profileData['avatar'] . '" alt="' . $profileData['display_name'] . '">
            </div>
            <div class="profile-stats">
                <div class="stat">
                    <div class="stat-number">' . $profileData['posts_count'] . '</div>
                    <div class="stat-label">publications</div>
                </div>
                <div class="stat">
                    <div class="stat-number">' . $profileData['followers_count'] . '</div>
                    <div class="stat-label">abonnés</div>
                </div>
                <div class="stat">
                    <div class="stat-number">' . $profileData['following_count'] . '</div>
                    <div class="stat-label">abonnements</div>
                </div>
            </div>
        </div>';

        // Génération du HTML pour les informations du profil (nom + bio + site web)
        $profileInfoHtml = '
        <div class="profile-info">
            <h2 class="profile-name">' . $profileData['display_name'] . '</h2>
            <div class="profile-bio">
                <p>' . str_replace('\n', '<br>', $profileData['bio']) . '</p>
                <p>
                    <a href="#" class="profile-link">
                        <img src="/images/instagram/svgs/link-outline.svg" alt="Link" class="icon" style="width: 16px; height: 16px;">
                        <span>' . $profileData['website'] . '</span>
                    </a>
                </p>
            </div>
        </div>';

        // Passage des données à la vue
        $view->addTemplateKey('PROFILE_DATA', $profileDataHtml);
        $view->addTemplateKey('PROFILE_INFO', $profileInfoHtml);
        $view->addTemplateKey('POSTS', $postsHtml);
        
        $view->render();
    }
    
    public function support(string $method): bool
    {
        return $method === 'GET';
    }
}

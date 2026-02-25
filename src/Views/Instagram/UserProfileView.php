<?php
namespace Views\Instagram;

/**
 * Vue pour le profil Instagram d'un utilisateur générique
 * 
 * Gère le rendu du template HTML du profil utilisateur.
 * Toute la génération HTML se fait ici (pas dans le controller).
 */
class UserProfileView extends BaseInstagramView
{
    private const TEMPLATE_PATH = __DIR__ . '/user-profile.html';

    private array $profileData = [];

    /**
     * Reçoit les données brutes du profil depuis le controller
     */
    public function setProfileData(array $data): void
    {
        $this->profileData = $data;
    }

    /**
     * @return string Chemin vers le template user-profile.html
     */
    public function templatePath(): string
    {
        return self::TEMPLATE_PATH;
    }

    /**
     * Génère toutes les clés de template pour le profil utilisateur
     *
     * @return array Clés incluant USERNAME, DISPLAY_NAME, AVATAR, stats, VERIFIED_ICON, PROFILE_INFO, POSTS
     */
    public function templateKeys(): array
    {
        $keys = parent::templateKeys();
        $data = $this->profileData;

        // Données scalaires
        $keys['USERNAME'] = htmlspecialchars($data['username']);
        $keys['DISPLAY_NAME'] = htmlspecialchars($data['display_name']);
        $keys['AVATAR'] = htmlspecialchars($data['avatar']);
        $keys['POSTS_COUNT'] = htmlspecialchars($data['posts_count']);
        $keys['FOLLOWERS_COUNT'] = htmlspecialchars($data['followers_count']);
        $keys['FOLLOWING_COUNT'] = htmlspecialchars($data['following_count']);

        // Icône de vérification
        $keys['VERIFIED_ICON'] = $data['verified']
            ? '<img src="/images/instagram/svgs/shield-checkmark.svg" alt="Vérifié" class="ionicon verified">'
            : '';

        // Génération du HTML pour les informations du profil
        $keys['PROFILE_INFO'] = $this->renderProfileInfo($data);

        // Génération du HTML pour les posts
        $keys['POSTS'] = $this->renderPosts($data['posts']);

        return $keys;
    }

    /**
     * Génère le HTML du bloc d'informations du profil (bio, site web)
     */
    private function renderProfileInfo(array $data): string
    {
        $html = '<div class="profile-info">'
            . '<h2 class="profile-name">' . htmlspecialchars($data['display_name']) . '</h2>'
            . '<div class="profile-bio">'
            . '<p>' . nl2br(htmlspecialchars($data['bio'])) . '</p>';

        if (!empty($data['website'])) {
            $html .= '<p><a href="#" class="profile-link">'
                . '<img src="/images/instagram/svgs/link-outline.svg" alt="Link" class="icon link-icon">'
                . '<span>' . htmlspecialchars($data['website']) . '</span>'
                . '</a></p>';
        }

        $html .= '</div></div>';
        return $html;
    }

    /**
     * Génère le HTML de la grille de posts
     */
    private function renderPosts(array $posts): string
    {
        $html = '';
        foreach ($posts as $post) {
            $videoIcon = isset($post['is_video']) && $post['is_video']
                ? '<img src="/images/instagram/svgs/videocam-outline.svg" alt="Vidéo" class="ionicon video_icon">'
                : '';

            // Si c'est une vidéo, on peut afficher une balise video (en mute loop) ou une image avec l'icône. 
            // Pour la grille de profil, on va garder une miniature (image) ou forcer la balise video selon le besoin.
            // On va essayer de charger la vidéo directement pour que le navigateur génère une miniature, ou utiliser l'image fallback.
            $mediaHtml = '';
            if (isset($post['is_video']) && $post['is_video'] && isset($post['video'])) {
                // Astuce : pour que Safari/iOS affiche la première frame d'une vidéo sans image de couverture, on ajoute #t=0.001
                $mediaHtml = '<video loading="lazy" src="' . $post['video'] . '#t=0.001" playsinline muted autoplay loop title="Post ' . $post['id'] . '" style="width: 100%; height: 100%; object-fit: cover;"></video>';
            } else {
                $mediaHtml = '<img loading="lazy" src="' . $post['image'] . '" alt="Post ' . $post['id'] . '" />';
            }

            $html .= '<figure class="figure">'
                . $videoIcon
                . $mediaHtml
                . '<figcaption class="access-hidden">Post</figcaption>'
                . '</figure>';
        }
        return $html;
    }
}


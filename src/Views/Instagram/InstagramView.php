<?php
namespace Views\Instagram;

/**
 * Vue pour la page d'accueil Instagram
 * 
 * Cette classe gère l'affichage du template HTML de l'accueil Instagram.
 * Elle hérite de BaseInstagramView pour les fonctionnalités communes.
 * 
 * Fonctionnalités :
 * - Gestion des clés de template ({{STORIES}}, {{POSTS}})
 * - Rendu sans header/footer CyberCigales (page standalone)
 */
class InstagramView extends BaseInstagramView
{
    private array $stories, $posts;
    public function __construct(array $stories, array $posts) {
        $this->stories = $stories;
        $this->posts = $posts;
    }

    // Chemin du fichier HTML template
    private const TEMPLATE_PATH = __DIR__ . '/instagram.html';

    private const KEY_STORIES = 'STORIES';

    private const KEY_POSTS = 'POSTS';

    /**
     * Méthode qui retourne le chemin du fichier HTML à utiliser pour le rendu
     * 
     * @return string Chemin vers le template HTML
     */
    public function templatePath() : string {
        return self::TEMPLATE_PATH;
    }

    public function templateKeys(): array
    {
        $keys = [];

        // ========================================
        // GÉNÉRATION DU HTML POUR LES STORIES
        // ========================================
        // On convertit les données PHP en HTML pour l'affichage
        // Chaque story devient un élément HTML avec avatar et nom d'utilisateur
        $storiesHtml = '';
        foreach($this->stories as $story) {
            // Si la story a une URL de redirection (comme Melina), on ajoute l'événement onclick
            $clickAction = isset($story['profile_url']) ? 'onclick="window.location.href=\'' . $story['profile_url'] . '\'"' : '';

            // Classe CSS pour les stories non vues (bordure colorée)
            $unseenClass = isset($story['is_unseen']) && $story['is_unseen'] ? 'story-unseen' : '';

            // Génération du HTML pour chaque story
            $storiesHtml .= '
            <div class="story-item" ' . $clickAction . ' style="cursor: pointer;">
                <div class="story-avatar ' . $unseenClass . '">
                    <img src="' . $story['avatar'] . '" alt="' . $story['username'] . '">
                </div>
                <span class="story-username">' . $story['username'] . '</span>
            </div>';
        }

        // ========================================
        // GÉNÉRATION DU HTML POUR LES POSTS
        // ========================================
        // On convertit chaque post en HTML avec tous ses éléments :
        // - Header (avatar, nom, localisation)
        // - Image du post
        // - Actions (like, commentaire, partage, sauvegarder)
        // - Contenu (likes, légende, commentaires, heure)
        $postsHtml = '';
        foreach($this->posts as $post) {
            // Génération des commentaires pour chaque post
            $commentsHtml = '';
            foreach($post['comments'] as $comment) {
                $commentsHtml .= '
                <div class="comment">
                    <span class="username">' . $comment['username'] . '</span>
                    <span>' . $comment['text'] . '</span>
                </div>';
            }

            $postsHtml .= '
            <article class="post">
                <div class="post-header">
                    <div class="user-info">
                        <img src="' . $post['avatar'] . '" alt="' . $post['username'] . '" class="user-avatar">
                        <div class="user-details">
                            <span class="username">' . $post['username'] . '</span>
                            <span class="location">' . $post['location'] . '</span>
                        </div>
                    </div>
                    <button class="more-btn">⋯</button>
                </div>
                
                <div class="post-image">
                    <img src="' . $post['image'] . '" alt="Post by ' . $post['username'] . '">
                </div>
                
                <div class="post-actions">
                    <button class="action-btn like-btn">
                        <img src="/images/instagram/svgs/heart-outline.svg" alt="Like" class="icon">
                    </button>
                    <button class="action-btn">
                        <img src="/images/instagram/svgs/chatbubble-outline.svg" alt="Comment" class="icon">
                    </button>
                    <button class="action-btn">
                        <img src="/images/instagram/svgs/repeat-outline.svg" alt="Share" class="icon">
                    </button>
                    <button class="action-btn bookmark-btn">
                        <img src="/images/instagram/svgs/grid-outline.svg" alt="Save" class="icon">
                    </button>
                </div>
                
                <div class="post-content">
                    <div class="likes">' . $post['likes'] . ' j\'aime</div>
                    <div class="caption">
                        <span class="username">' . $post['username'] . '</span>
                        <span>' . $post['caption'] . '</span>
                    </div>
                    <div class="comments">' . $commentsHtml . '</div>
                    <div class="time">' . $post['time'] . '</div>
                </div>
            </article>';
        }

        $keys[self::KEY_STORIES] = $storiesHtml;
        $keys[self::KEY_POSTS] = $postsHtml;

        return $keys;
    }
}

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

    /**
     * @param array $stories Tableau des stories Instagram
     * @param array $posts   Tableau des posts du feed
     */
    public function __construct(array $stories, array $posts)
    {
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
    public function templatePath(): string
    {
        return self::TEMPLATE_PATH;
    }

    /**
     * Clés injectées dans le template Instagram
     *
     * @return array{STORIES: string, POSTS: string}
     */
    public function templateKeys(): array
    {
        $keys = [];
        $keys[self::KEY_STORIES] = $this->renderStories();
        $keys[self::KEY_POSTS] = $this->renderPosts();
        return $keys;
    }

    /**
     * Génère le HTML pour les stories
     */
    private function renderStories(): string
    {
        $html = '';
        foreach ($this->stories as $story) {
            $unseenClass = !empty($story['is_unseen']) ? 'story-unseen' : '';
            $profileUrl = $story['profile_url'] ?? '#';

            $html .= '<a href="' . $profileUrl . '" class="story-item">'
                . '<div class="story-avatar ' . $unseenClass . '">'
                . '<img src="' . $story['avatar'] . '" alt="' . $story['username'] . '">'
                . '</div>'
                . '<span class="story-username">' . $story['username'] . '</span>'
                . '</a>';
        }
        return $html;
    }

    /**
     * Génère le HTML pour les posts du feed
     */
    private function renderPosts(): string
    {
        $html = '';
        foreach ($this->posts as $post) {
            $commentsHtml = $this->renderComments($post['comments']);

            $mediaHtml = '';
            if (isset($post['is_video']) && $post['is_video'] && isset($post['video'])) {
                $mediaHtml = '<video src="' . $post['video'] . '" title="Post by ' . $post['username'] . '" autoplay muted loop playsinline controls style="width: 100%; max-height: 600px; object-fit: contain; background: black;"></video>';
            } else {
                $mediaHtml = '<img src="' . $post['image'] . '" alt="Post by ' . $post['username'] . '">';
            }

            $html .= '<article class="post">'
                . '<div class="post-header">'
                . '<div class="user-info">'
                . '<img src="' . $post['avatar'] . '" alt="' . $post['username'] . '" class="user-avatar">'
                . '<div class="user-details">'
                . '<span class="username">' . $post['username'] . '</span>'
                . '<span class="location">' . $post['location'] . '</span>'
                . '</div></div>'
                . '<button class="more-btn" aria-label="Plus d\'options">&#x22EF;</button>'
                . '</div>'
                . '<div class="post-media" style="width: 100%; display: flex; justify-content: center; background: #fafafa;">'
                . $mediaHtml
                . '</div>'
                . '<div class="post-actions">'
                . '<button class="action-btn like-btn" aria-label="Aimer"><img src="/images/instagram/svgs/heart-outline.svg" alt="Like" class="icon"></button>'
                . '<button class="action-btn" aria-label="Commenter"><img src="/images/instagram/svgs/chatbubble-outline.svg" alt="Comment" class="icon"></button>'
                . '<button class="action-btn" aria-label="Partager"><img src="/images/instagram/svgs/repeat-outline.svg" alt="Share" class="icon"></button>'
                . '<button class="action-btn bookmark-btn" aria-label="Sauvegarder"><img src="/images/instagram/svgs/grid-outline.svg" alt="Save" class="icon"></button>'
                . '</div>'
                . '<div class="post-content">'
                . '<div class="likes">' . $post['likes'] . ' j\'aime</div>'
                . '<div class="caption">'
                . '<span class="username">' . $post['username'] . '</span>'
                . '<span>' . $post['caption'] . '</span>'
                . '</div>'
                . '<div class="comments">' . $commentsHtml . '</div>'
                . '<div class="time">' . $post['time'] . '</div>'
                . '</div></article>';
        }
        return $html;
    }

    /**
     * Génère le HTML pour les commentaires d'un post
     */
    private function renderComments(array $comments): string
    {
        $html = '';
        foreach ($comments as $comment) {
            $html .= '<div class="comment">'
                . '<span class="username">' . $comment['username'] . '</span>'
                . '<span>' . $comment['text'] . '</span>'
                . '</div>';
        }
        return $html;
    }
}

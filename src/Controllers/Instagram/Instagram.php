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
        // $this->connexionVerify();

        // Création des instances MVC
        $view = new InstagramView();
        $model = new InstagramModel();
        
        // ========================================
        // RÉCUPÉRATION DES DONNÉES VIA LE MODÈLE
        // ========================================
        // Le modèle gère toutes les données statiques et dynamiques
        $stories = $model->getStories();
        
        // Récupération des posts via le modèle
        $posts = $model->getPosts();
        
        // ========================================
        // GÉNÉRATION DU HTML POUR LES STORIES
        // ========================================
        // On convertit les données PHP en HTML pour l'affichage
        // Chaque story devient un élément HTML avec avatar et nom d'utilisateur
        $storiesHtml = '';
        foreach($stories as $story) {
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
        foreach($posts as $post) {
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
        
        // ========================================
        // INJECTION DES DONNÉES DANS LA VUE
        // ========================================
        // On passe les HTML générés à la vue via des clés de template
        // Ces clés remplaceront les placeholders {{STORIES}} et {{POSTS}} dans le template HTML
        $view->addTemplateKey('STORIES', $storiesHtml); // Remplace {{STORIES}} dans le template
        $view->addTemplateKey('POSTS', $postsHtml);     // Remplace {{POSTS}} dans le template
        
        // Affichage final de la page Instagram
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

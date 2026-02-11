<?php
namespace Controllers\Instagram;

use Views\Instagram\InstagramView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

class Index extends AbstractController
{
    function getMethod(){
        $this->connexionVerify();
        $view = new InstagramView();
        $model = new InstagramModel();
        $stories = $model->getStories();
        $posts = $model->getPosts();
        
        $storiesHtml = '';
        foreach($stories as $story) {
            $clickAction = isset($story['profile_url']) ? 'onclick="window.location.href=\'' . $story['profile_url'] . '\'"' : '';
            $unseenClass = isset($story['is_unseen']) && $story['is_unseen'] ? 'story-unseen' : '';
            $storiesHtml .= '
            <div class="story-item" ' . $clickAction . ' style="cursor: pointer;">
                <div class="story-avatar ' . $unseenClass . '">
                    <img src="' . $story['avatar'] . '" alt="' . $story['username'] . '">
                </div>
                <span class="story-username">' . $story['username'] . '</span>
            </div>';
        }
        
        $postsHtml = '';
        foreach($posts as $post) {
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
        
        $view->addTemplateKey('STORIES', $storiesHtml);
        $view->addTemplateKey('POSTS', $postsHtml);
        $view->render();
    }
    
    static function support(string $chemin, string $method) : bool{
        return $chemin === "/instagram" && $method === "GET";
    }
}

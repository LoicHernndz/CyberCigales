<?php
namespace Controllers\Instagram;

use Views\Instagram\UserChatView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

/**
 * Contrôleur pour le chat avec un utilisateur Instagram générique
 */
class UserChat extends AbstractController
{
    function getMethod(){
        // Récupérer le username depuis l'URL
        // URL format: /instagram/user/{username}/chat
        $uri = $_SERVER['REQUEST_URI'];
        $parts = explode('/', trim($uri, '/'));
        
        // Trouver l'index de "user" et récupérer le username
        $userIndex = array_search('user', $parts);
        $username = $parts[$userIndex + 1] ?? null;
        
        // Supprimer les paramètres GET si présents
        if ($username && strpos($username, '?') !== false) {
            $username = explode('?', $username)[0];
        }
        
        // Création des instances MVC
        $view = new UserChatView();
        $model = new InstagramModel();
        
        // Récupération du profil utilisateur
        $userProfile = $model->getUserProfile($username);
        
        // Si le profil n'existe pas, rediriger vers la page Instagram
        if ($userProfile === null) {
            header('Location: /instagram');
            exit;
        }
        
        // Récupération des messages du chat
        $chatMessages = $model->getUserChatMessages($username);
        
        // Ajout du statut pour le chat
        $userProfile['status'] = 'En ligne';
        
        // Passage des données à la vue
        $view->addTemplateKey('USER_AVATAR', $userProfile['avatar']);
        $view->addTemplateKey('USER_DISPLAY_NAME', $userProfile['display_name']);
        $view->addTemplateKey('USER_USERNAME', $userProfile['username']);
        $view->addTemplateKey('USER_STATUS', $userProfile['status']);
        $view->addTemplateKey('PROFILE_URL', '/instagram/user/' . urlencode($username));

        // Génération du HTML pour les messages
        $messagesHtml = '';
        foreach($chatMessages as $message) {
            $senderClass = ($message['type'] === 'sent') ? 'sent' : 'received';
            $messagesHtml .= '
            <div class="message ' . $senderClass . '">
                <div class="message-content">
                    <p>' . htmlspecialchars($message['content']) . '</p>
                    <span class="time">' . $message['time'] . '</span>
                </div>
            </div>';
        }
        
        // Passage des données à la vue
        $view->addTemplateKey('MESSAGES', $messagesHtml);
        
        $view->render();
    }
    
    public function support(string $method): bool
    {
        return $method === 'GET';
    }
}


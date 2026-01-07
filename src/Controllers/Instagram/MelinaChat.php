<?php
namespace Controllers\Instagram;

use Views\Instagram\MelinaChatView;
use Models\Instagram\InstagramModel;
use Models\Chat\UserChatProgress;
use Controllers\AbstractController;

class MelinaChat extends AbstractController
{
    function getMethod(){
        // Démarrer la session si pas déjà fait
        if (!isset($_SESSION)) {
            session_start();
        }

        // Création des instances MVC
        $view = new MelinaChatView();
        $model = new InstagramModel();
        
        // ========================================
        // RÉCUPÉRATION DE LA PROGRESSION UTILISATEUR
        // ========================================
        $progressIndex = 0; // Par défaut niveau 0
        
        if (isset($_SESSION['user_id'])) {
            $progressModel = new UserChatProgress();
            $progressIndex = $progressModel->getProgressIndex($_SESSION['user_id'], 'melina');
        }
        
        // ========================================
        // RÉCUPÉRATION DES DONNÉES VIA LE MODÈLE
        // ========================================
        $melinaInfo = $model->getMelinaProfile();
        $melinaInfo['status'] = 'En ligne'; // Ajout du statut pour le chat
        
        // Récupérer les messages selon le niveau de progression
        $chatMessages = $model->getMelinaChatMessages($progressIndex);
        
        // ========================================
        // GÉNÉRATION DU HTML POUR LE HEADER DU CHAT
        // ========================================
        $view->addTemplateKey('MELINA_AVATAR', $melinaInfo['avatar']);
        $view->addTemplateKey('MELINA_DISPLAY_NAME', $melinaInfo['display_name']);
        $view->addTemplateKey('MELINA_STATUS', $melinaInfo['status']);

        // ========================================
        // GÉNÉRATION DU HTML POUR LES MESSAGES
        // ========================================
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

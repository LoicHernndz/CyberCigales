<?php
namespace Controllers\Instagram;

use Views\Instagram\MelinaChatView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

class MelinaChat extends AbstractController
{
    function getMethod(){
        // Création des instances MVC
        $view = new MelinaChatView();
        $model = new InstagramModel();
        
        // ========================================
        // RÉCUPÉRATION DES DONNÉES VIA LE MODÈLE
        // ========================================
        $melinaInfo = $model->getMelinaProfile();
        $melinaInfo['status'] = 'En ligne'; // Ajout du statut pour le chat
        
        $chatMessages = $model->getMelinaChatMessages();
        
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
    
    function postMethod(){
        // Vérifier si c'est une requête AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        $model = new InstagramModel();
        $response = ['success' => false, 'message' => ''];
        
        // Récupérer le message depuis POST
        $messageContent = $_POST['message'] ?? '';
        $messageContent = trim($messageContent);
        
        if (empty($messageContent)) {
            $response['message'] = 'Le message ne peut pas être vide';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            }
        }
        
        // Sauvegarder le message de l'utilisateur
        $saved = $model->saveMessage('sent', $messageContent);
        
        if ($saved) {
            // Générer une réponse automatique de Melina
            $responses = [
                "C'est super ! 😊",
                "J'adore ça ! ✨",
                "Merci pour ton message ! 💕",
                "C'est génial ! 🎉",
                "Parfait ! 👍",
                "J'aime beaucoup ! 💖",
                "C'est magnifique ! 🌟",
                "Excellent ! 👏",
                "Trop cool ! 🔥",
                "J'adore ! 😍"
            ];
            $melinaResponse = $responses[array_rand($responses)];
            
            // Sauvegarder la réponse de Melina
            $model->saveMessage('received', $melinaResponse);
            
            $response['success'] = true;
            $response['userMessage'] = [
                'type' => 'sent',
                'content' => $messageContent,
                'time' => date('H:i')
            ];
            $response['melinaMessage'] = [
                'type' => 'received',
                'content' => $melinaResponse,
                'time' => date('H:i')
            ];
        } else {
            $response['message'] = 'Erreur lors de la sauvegarde du message';
        }
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        // Si ce n'est pas AJAX, rediriger vers la page du chat
        header('Location: /instagram/melina/chat');
        exit;
    }
    
    public function support(string $method): bool
    {
        return $method === 'GET' || $method === 'POST';
    }
}

<?php
namespace Controllers\Instagram;

use Views\Instagram\MelinaChatView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

class MelinaChat extends AbstractController
{
    function getMethod(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $view = new MelinaChatView();
        $model = new InstagramModel();
        
        $melinaInfo = $model->getMelinaProfile();
        $melinaInfo['status'] = 'En ligne';
        
        $view->addTemplateKey('MELINA_AVATAR', $melinaInfo['avatar']);
        $view->addTemplateKey('MELINA_DISPLAY_NAME', $melinaInfo['display_name']);
        $view->addTemplateKey('MELINA_STATUS', $melinaInfo['status']);
        $view->addTemplateKey('MESSAGES', '');
        
        $view->render();
    }
    
    function postMethod(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        $model = new InstagramModel();
        $response = ['success' => false, 'message' => ''];
        
        $conversationId = $_POST['conv_id'] ?? '';
        
        if (empty($conversationId)) {
            $response['message'] = 'ID de conversation manquant';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            }
            header('Location: /instagram/melina/chat');
            exit;
        }
        
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        if ($action === 'load') {
            $messages = $model->getMelinaChatMessages($conversationId);
            $response = [
                'success' => true,
                'messages' => $messages
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        $messageContent = trim($_POST['message'] ?? '');
        
        if (empty($messageContent)) {
            $response['message'] = 'Le message ne peut pas être vide';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            }
        }
        
        // Sauvegarder le message de l'utilisateur pour cette conversation
        $saved = $model->saveMessage('sent', $messageContent, $conversationId);
        
        if ($saved) {
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
            
            $model->saveMessage('received', $melinaResponse, $conversationId);
            
            $response['conv_id'] = $conversationId;
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
        
        header('Location: /instagram/melina/chat');
        exit;
    }
    
    public function support(string $method): bool
    {
        return $method === 'GET' || $method === 'POST';
    }
}
